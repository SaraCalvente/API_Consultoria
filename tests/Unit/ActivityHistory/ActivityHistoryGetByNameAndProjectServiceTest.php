<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByNameAndProjectService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\User\Domain\User;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryGetByNameAndProjectServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testReturns201WhenActivityIsFound(): void
    {
        $data = [
            'name' => 'Activity 1',
            'projectName' => 'Project A'
        ];

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity = $this->createMock(ActivityHistory::class);
        $activity->method('getId')->willReturn(1);
        $activity->method('getName')->willReturn('Activity 1');
        $activity->method('getDescription')->willReturn('Description');
        $activity->method('getDate')->willReturn(new \DateTime('2025-04-16'));
        $activity->method('getProject')->willReturn($project);
        $activity->method('getUser')->willReturn($user);

        $projectRepo = $this->createMock(ProjectRepositoryInterface::class);
        $projectRepo->method('findProjectByName')->with('Project A')->willReturn($project);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $activityRepo->method('checkIfActivityHistoryFromProjectExists')->with('Activity 1', $project)->willReturn(true);
        $activityRepo->method('findActivityHistoryFromProject')->with('Activity 1', $project)->willReturn($activity);

        $service = new ActivityHistoryGetByNameAndProjectService($activityRepo, $projectRepo);
        $response = $service($data);

        $this->assertEquals(201, $response->getStatusCode());

        $expected = [
            'message' => 'Tasks retrieved successfully',
            'task' => [
                'activity_history_id' => 1,
                'name' => 'Activity 1',
                'description' => 'Description',
                'project_id' => 300,
                'date' => '2025-04-16',
                'user_id' => 200,
            ]
        ];

        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }


    /**
     * @throws Exception
     */
    public function testThrowsExceptionWhenProjectNotFound(): void
    {
        $this->expectException(ProjectNotFoundException::class);

        $data = [
            'name' => 'Activity 1',
            'projectName' => 'NonExistent Project'
        ];

        $projectRepo = $this->createMock(ProjectRepositoryInterface::class);
        $projectRepo->method('findProjectByName')->with('NonExistent Project')->willReturn(null);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);

        $service = new ActivityHistoryGetByNameAndProjectService($activityRepo, $projectRepo);
        $service($data);
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionWhenActivityNotFound(): void
    {
        $this->expectException(ActivityHistoryNotFoundException::class);

        $data = [
            'name' => 'Unknown Activity',
            'projectName' => 'Project A'
        ];

        $project = $this->createMock(Project::class);

        $projectRepo = $this->createMock(ProjectRepositoryInterface::class);
        $projectRepo->method('findProjectByName')->with('Project A')->willReturn($project);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $activityRepo->method('checkIfActivityHistoryFromProjectExists')->with('Unknown Activity', $project)->willReturn(false);

        $service = new ActivityHistoryGetByNameAndProjectService($activityRepo, $projectRepo);
        $service($data);
    }
}
