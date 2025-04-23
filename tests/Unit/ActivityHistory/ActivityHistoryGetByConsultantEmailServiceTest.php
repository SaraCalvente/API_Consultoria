<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByConsultantEmailService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\User;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryGetByConsultantEmailServiceTest extends Unit
{
    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testReturns400IfUserIsNotConsultant(): void
    {
        $data = ['email' => 'test@example.com'];
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject($data['email']));

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('findUserByEmail')->willReturn($user);

        $consultantRepo = $this->createMock(ConsultantRepositoryInterface::class);
        $consultantRepo->method('checkIfConsultantExists')->with($user)->willReturn(false);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);

        $service = new ActivityHistoryGetByConsultantEmailService($activityRepo, $consultantRepo, $userRepo);
        $response = $service($data);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['error' => 'The user test@example.com is not a consultant'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testReturns402IfConsultantNotFound(): void
    {
        $data = ['email' => 'consultant@example.com'];

        $user = $this->createMock(User::class);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('findUserByEmail')->willReturn($user);

        $consultantRepo = $this->createMock(ConsultantRepositoryInterface::class);
        $consultantRepo->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $consultantRepo->method('findConsultantByUser')->willReturn(null);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);

        $service = new ActivityHistoryGetByConsultantEmailService($activityRepo, $consultantRepo, $userRepo);
        $response = $service($data);

        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals(['error' => 'Consultant has no associated activities'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testReturnsActivitiesSuccessfully(): void
    {
        $data = ['email' => 'consultant@example.com'];

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);
        $user->method('getEmail')->willReturn(new EmailValueObject($data['email']));

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(500);

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $activity = $this->createMock(ActivityHistory::class);
        $activity->method('getId')->willReturn(1);
        $activity->method('getName')->willReturn('Activity');
        $activity->method('getDescription')->willReturn('Description');
        $activity->method('getDate')->willReturn(new \DateTime('2025-04-16'));
        $activity->method('getProject')->willReturn($project);
        $activity->method('getUser')->willReturn($user);

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $userRepo->method('findUserByEmail')->willReturn($user);

        $consultantRepo = $this->createMock(ConsultantRepositoryInterface::class);
        $consultantRepo->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $consultantRepo->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $activityRepo->method('findActivitiesByConsultant')->with($user)->willReturn([$activity]);

        $service = new ActivityHistoryGetByConsultantEmailService($activityRepo, $consultantRepo, $userRepo);
        $response = $service($data);

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
            ]]
        ];
        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }
}
