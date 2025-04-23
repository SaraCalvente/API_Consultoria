<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetAllService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryGetAllServiceTest extends Unit
{
    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testInvokeReturnsAllActivitiesAsJsonResponse(): void
    {
        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(100);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity1 = $this->createActivityMock(1, 'Actividad 1', 'Descripción 1', '2025-04-16', $project, $user);
        $activity2 = $this->createActivityMock(2, 'Actividad 2', 'Descripción 2', '2025-04-17', $project, $user);

        $repository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $repository->method('findAllActivityHistories')->willReturn([$activity1, $activity2]);

        $service = new ActivityHistoryGetAllService($repository);
        $response = $service();

        $this->assertInstanceOf(JsonResponse::class, $response);

        $expected = [
            'message' => 'Activities retrieved successfully',
            'tasks' => [
                [
                    'activity_history_id' => 1,
                    'name' => 'Actividad 1',
                    'description' => 'Descripción 1',
                    'project_id' => 100,
                    'date' => '2025-04-16',
                    'user_id' => 200,
                ],
                [
                    'activity_history_id' => 2,
                    'name' => 'Actividad 2',
                    'description' => 'Descripción 2',
                    'project_id' => 100,
                    'date' => '2025-04-17',
                    'user_id' => 200,
                ],
            ]
        ];

        $this->assertEquals($expected, json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testInvokeReturns404IfNoActivities(): void
    {
        $repository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $repository->method('findAllActivityHistories')->willReturn([]);

        $service = new ActivityHistoryGetAllService($repository);
        $response = $service();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(['error' => 'There are no activities'], json_decode($response->getContent(), true));
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
