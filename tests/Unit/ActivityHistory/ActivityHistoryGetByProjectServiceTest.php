<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByProjectService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryByProjectDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;

class ActivityHistoryGetByProjectServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;

    private ActivityHistoryGetByProjectService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ActivityHistoryGetByProjectService($this->activityHistoryRepository, $this->projectRepository);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturnsActivitiesByProjectSuccessfully(): void
    {
        $projectName = 'Project A';
        $dto = new ActivityHistoryByProjectDTO($projectName);

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity1 = $this->createActivityMock(1, 'Activity 1', 'Description 1', '2025-04-16', $project, $user);
        $activity2 = $this->createActivityMock(2, 'Activity 2', 'Description 2', '2025-04-17', $project, $user);

        $this->projectRepository->method('checkIfProjectExists')->with($projectName)->willReturn(true);
        $this->projectRepository->method('findProjectByName')->with($projectName)->willReturn($project);

        $this->activityHistoryRepository
            ->method('findActivitiesHistoriesByProject')
            ->with($project)
            ->willReturn([$activity1, $activity2]);

        $result = ($this->service)($dto);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(ActivityHistoryDTO::class, $result[0]);
        $this->assertSame('Activity 1', $result[0]->name);
        $this->assertSame('Activity 2', $result[1]->name);
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionIfProjectNotFound(): void
    {
        $dto = new ActivityHistoryByProjectDTO('Nonexistent Project');

        $this->projectRepository->method('checkIfProjectExists')->with('Nonexistent Project')->willReturn(false);

        $this->expectException(ProjectNotFoundException::class);

        ($this->service)($dto);
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
    ) {
        $activity = $this->createMock(\App\ActivityHistory\Domain\ActivityHistory::class);
        $activity->method('getId')->willReturn($id);
        $activity->method('getName')->willReturn($name);
        $activity->method('getDescription')->willReturn($description);
        $activity->method('getDate')->willReturn(new \DateTime($date));
        $activity->method('getProject')->willReturn($project);
        $activity->method('getUser')->willReturn($user);

        return $activity;
    }
}
