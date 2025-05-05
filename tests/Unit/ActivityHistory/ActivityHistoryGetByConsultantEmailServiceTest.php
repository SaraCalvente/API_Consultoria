<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Application\ActivityHistoryGetByConsultantEmailService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\User;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use DomainException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryGetByConsultantEmailServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private UserRepositoryInterface $userRepository;
    private ConsultantRepositoryInterface $consultantRepository;

    private ActivityHistoryGetByConsultantEmailService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new ActivityHistoryGetByConsultantEmailService($this->activityHistoryRepository, $this->consultantRepository, $this->userRepository);
        $this->data = ['email' => 'test@example.com'];

    }

    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testReturns400IfUserIsNotConsultant(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject($this->data['email']));

        $this->userRepository->method('findUserByEmailOrFail')->willReturn($user);

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(false);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("The user test@example.com is not a consultant");

        ($this->service)($this->data);
    }


    /**
     * @throws Exception
     */
    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testReturns402IfConsultantNotFound(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject($this->data['email']));

        $this->userRepository->method('findUserByEmailOrFail')->willReturn($user);

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Consultant not found for user: test@example.com");

        ($this->service)($this->data);
    }


    /**
     * @throws Exception
     * @throws NotValidEmailException
     * @throws \DateMalformedStringException
     */
    public function testReturnsActivitiesSuccessfully(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);
        $user->method('getEmail')->willReturn(new EmailValueObject($this->data['email']));

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(500);

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $activity = $this->createActivityMock(1, 'Activity', 'Description', '2025-04-16', $project, $user);

        $this->userRepository->method('findUserByEmailOrFail')->willReturn($user);

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->activityHistoryRepository->method('findActivitiesByConsultant')->with($user)->willReturn([$activity]);

        $result = ($this->service)($this->data);

        $this->assertEquals(500, $result['current_consultant_id']);
        $this->assertCount(1, $result['activities']);
        $this->assertEquals('Activity', $result['activities'][0]->name);
        $this->assertEquals('Description', $result['activities'][0]->description);
        $this->assertEquals('2025-04-16', $result['activities'][0]->date);
        $this->assertEquals(300, $result['activities'][0]->projectId);
        $this->assertEquals(200, $result['activities'][0]->userId);
    }

    /**
     * @throws Exception
     * @throws \DateMalformedStringException
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
