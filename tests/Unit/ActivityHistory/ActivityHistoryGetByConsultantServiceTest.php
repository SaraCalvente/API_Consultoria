<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByConsultantService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use DomainException;

class ActivityHistoryGetByConsultantServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private ActivityHistoryGetByConsultantService $service;
    private string $email;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new ActivityHistoryGetByConsultantService(
            $this->consultantRepository,
            $this->activityHistoryRepository
        );
        $this->email = 'test@example.com';
    }

    /**
     * @throws NotValidEmailException
     * @throws Exception
     */
    public function testThrowsExceptionIfUserIsNotConsultant(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject($this->email));

        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('The user test@example.com is not a consultant');

        ($this->service)($user);
    }

    /**
     * @throws Exception
     */
    public function testReturnsActivitiesSuccessfully(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);
        $user->method('getEmail')->willReturn(new EmailValueObject('consultant@example.com'));

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(500);

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $activity = $this->createActivityMock(1, 'Activity', 'Description', '2025-04-16', $project, $user);

        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);
        $this->activityHistoryRepository->method('findActivitiesByConsultant')->with($user)->willReturn([$activity]);

        $result = ($this->service)($user);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('current_consultant_id', $result);
        $this->assertArrayHasKey('activities', $result);

        $this->assertEquals(500, $result['current_consultant_id']);
        $this->assertCount(1, $result['activities']);
        $this->assertInstanceOf(ActivityHistoryDTO::class, $result['activities'][0]);

        $this->assertEquals('Activity', $result['activities'][0]->name);
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    private function createActivityMock(
        int $id,
        string $name,
        string $description,
        string $date,
        ?Project $project,
        ?User $user
    ): ActivityHistory {
        $activity = $this->createMock(ActivityHistory::class);
        $activity->method('getId')->willReturn($id);
        $activity->method('getName')->willReturn($name);
        $activity->method('getDescription')->willReturn($description);
        $activity->method('getDate')->willReturn(new \DateTime($date));
        $activity->method('getProject')->willReturn($project);
        $activity->method('getUser')->willReturn($user);

        return $activity;
    }
}
