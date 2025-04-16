<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryUpdateByNameAndProjectService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Domain\User;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryUpdateByNameAndProjectServiceTest extends TestCase
{
    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testUpdatesActivityDescriptionSuccessfully(): void
    {
        $data = [
            'projectName' => 'Project A',
            'name' => 'Activity 1',
            'description' => 'New updated description'
        ];

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity = $this->createMock(ActivityHistory::class);
        $activity->method('getId')->willReturn(1);
        $activity->method('getName')->willReturn('Activity 1');
        $activity->method('getDescription')->willReturn('New updated description');
        $activity->method('getDate')->willReturn(new \DateTime('2025-04-16'));
        $activity->method('getProject')->willReturn($project);
        $activity->method('getUser')->willReturn($user);

        $activity->expects($this->once())
            ->method('setDescription')
            ->with('New updated description');

        $projectRepo = $this->createMock(ProjectRepositoryInterface::class);
        $projectRepo->method('findProjectByName')->with('Project A')->willReturn($project);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $activityRepo->method('findActivityHistoryFromProject')->with('Activity 1', $project)->willReturn($activity);
        $activityRepo->expects($this->once())->method('saveActivityHistory');

        $service = new ActivityHistoryUpdateByNameAndProjectService($activityRepo, $projectRepo);
        $response = $service($data);

        $this->assertEquals(201, $response->getStatusCode());

        $expected = [
            'message' => 'Activity updated successfully',
            'project' => [
                'activity_history_id' => 1,
                'name' => 'Activity 1',
                'description' => 'New updated description',
                'project_id' => 300,
                'date' => '2025-04-16',
                'user_id' => 200,
            ]
        ];

        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }
}
