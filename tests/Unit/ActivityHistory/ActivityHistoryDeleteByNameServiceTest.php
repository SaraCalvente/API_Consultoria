<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryDeleteByNameService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryDeleteByNameServiceTest extends Unit
{
    /**
     * @throws Exception
     */
    public function testDeletesActivitySuccessfully(): void
    {
        $data = [
            'projectName' => 'Project A',
            'name' => 'Activity 1'
        ];

        $project = $this->createMock(Project::class);

        $activity = $this->createMock(ActivityHistory::class);

        $projectRepo = $this->createMock(ProjectRepositoryInterface::class);
        $projectRepo->method('findProjectByName')->with('Project A')->willReturn($project);

        $activityRepo = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $activityRepo->method('findActivityHistoryFromProject')->with('Activity 1', $project)->willReturn($activity);
        $activityRepo->expects($this->once())->method('removeActivityHistory')->with($activity);

        $service = new ActivityHistoryDeleteByNameService($activityRepo, $projectRepo);
        $response = $service($data);

        $this->assertEquals(201, $response->getStatusCode());

        $expected = ['message' => 'Activity deleted successfully'];
        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }
}
