<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByConsultantEmailService;
use App\ActivityHistory\Application\ActivityHistoryGetByConsultantService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        $this->service = new ActivityHistoryGetByConsultantService($this->consultantRepository, $this->activityHistoryRepository);
        $this->email = 'test@example.com';

    }
    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testReturns402IfUserIsNotConsultant(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject($this->email));

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(false);

        $response = ($this->service)($user);

        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals(['error' => 'The user test@example.com is not a consultant'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testReturns400IfConsultantNotFound(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject($this->email));

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $this->consultantRepository->method('findConsultantByUser')->willReturn(null);

        $response = ($this->service)($user);

        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals(['error' => 'The user test@example.com is not a consultant'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     * @throws \DateMalformedStringException
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

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $this->activityHistoryRepository->method('findActivitiesByConsultant')->with($user)->willReturn([$activity]);

        $response = ($this->service)($user);

        $this->assertEquals(200, $response->getStatusCode());

        $expected = [
            'message' => 'Tasks retrieved successfully',
            'current_consultant_id' => 500,
            'activities' => [[
                'activity_history_id' => 1,
                'name' => 'Activity',
                'description' => 'Description',
                'project_id' => 300,
                'date' => '2025-04-16',
                'user_id' => 200,
            ]],
        ];

        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }

    /**
     * @throws \DateMalformedStringException
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
