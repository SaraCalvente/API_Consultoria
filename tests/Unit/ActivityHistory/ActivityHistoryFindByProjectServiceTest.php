<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryFindByProjectService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Domain\User;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryFindByProjectServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testReturnsActivitiesByProjectSuccessfully(): void
    {
        $data = ['projectName' => 'Project A'];

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity1 = $this->createMock(ActivityHistory::class);
        $activity1->method('getId')->willReturn(1);
        $activity1->method('getName')->willReturn('Activity 1');
        $activity1->method('getDescription')->willReturn('Description 1');
        $activity1->method('getDate')->willReturn(new \DateTime('2025-04-16'));
        $activity1->method('getProject')->willReturn($project);
        $activity1->method('getUser')->willReturn($user);

        $activity2 = $this->createMock(ActivityHistory::class);
        $activity2->method('getId')->willReturn(2);
        $activity2->method('getName')->willReturn('Activity 2');
        $activity2->method('getDescription')->willReturn('Description 2');
        $activity2->method('getDate')->willReturn(new \DateTime('2025-04-17'));
        $activity2->method('getProject')->willReturn($project);
        $activity2->method('getUser')->willReturn($user);

        $projectRepo = $this->createMock(ProjectRepositoryInterface::class);
        $projectRepo->method('findProjectByName')->with('Project A')->willReturn($project);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $activityRepo->method('findActivitiesHistoriesByProject')->with($project)->willReturn([$activity1, $activity2]);

        $service = new ActivityHistoryFindByProjectService($activityRepo, $projectRepo);
        $response = $service($data);

        $this->assertEquals(201, $response->getStatusCode());

        $expected = [
            'message' => 'Tasks retrieved successfully',
            'tasks' => [
                [
                    'activity_history_id' => 1,
                    'name' => 'Activity 1',
                    'description' => 'Description 1',
                    'project_id' => 300,
                    'date' => '2025-04-16',
                    'user_id' => 200,
                ],
                [
                    'activity_history_id' => 2,
                    'name' => 'Activity 2',
                    'description' => 'Description 2',
                    'project_id' => 300,
                    'date' => '2025-04-17',
                    'user_id' => 200,
                ]
            ]
        ];

        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }
}
