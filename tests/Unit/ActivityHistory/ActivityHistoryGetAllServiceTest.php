<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetAllService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Domain\User;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;

class ActivityHistoryGetAllServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ActivityHistoryGetAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->service = new ActivityHistoryGetAllService($this->activityHistoryRepository);
    }

    /**
     * @throws Exception
     */
    public function testInvokeReturnsAllActivitiesAsArray(): void
    {
        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(100);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity1 = $this->createActivityMock(1, 'Actividad 1', 'Descripción 1', '2025-04-16', $project, $user);
        $activity2 = $this->createActivityMock(2, 'Actividad 2', 'Descripción 2', '2025-04-17', $project, $user);

        $this->activityHistoryRepository->method('findAllActivityHistories')->willReturn([$activity1, $activity2]);

        $result = ($this->service)();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(ActivityHistoryDTO::class, $result[0]);
        $this->assertEquals('Actividad 1', $result[0]->name);
        $this->assertEquals('Descripción 1', $result[0]->description);
        $this->assertEquals(100, $result[0]->projectId);
        $this->assertEquals('2025-04-16', $result[0]->date);
        $this->assertEquals(200, $result[0]->userId);
    }

    /**
     * @throws Exception
     */
    public function testInvokeThrowsExceptionIfNoActivitiesFound(): void
    {
        $this->activityHistoryRepository->method('findAllActivityHistories')->willReturn([]);

        $this->expectException(ActivityHistoryNotFoundException::class);

        ($this->service)();
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    private function createActivityMock(
        int $id,
        string $name,
        string $description,
        string $date,
        ?Project $project,
        ?User $user
    ): ActivityHistory
    {
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
