<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryDeleteByNameService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDeleteDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\ActivityHistory\Domain\ActivityHistory;
use Codeception\Test\Unit;
use DomainException;
use PHPUnit\Framework\MockObject\Exception;

class ActivityHistoryDeleteByNameServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ActivityHistoryDeleteByNameService $service;
    private ActivityHistoryDeleteDTO $dto;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ActivityHistoryDeleteByNameService(
            $this->activityHistoryRepository,
            $this->projectRepository
        );

        $this->dto = new ActivityHistoryDeleteDTO('Project A', 'Activity 1');
    }

    /**
     * @throws Exception
     */
    public function testDeletesActivitySuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $activity = $this->createMock(ActivityHistory::class);

        $this->projectRepository->method('checkIfProjectExists')->with('Project A')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->with('Project A')->willReturn($project);

        $this->activityHistoryRepository
            ->method('checkIfActivityHistoryFromProjectExists')
            ->with('Activity 1', $project)
            ->willReturn(true);

        $this->activityHistoryRepository
            ->method('findActivityHistoryFromProject')
            ->with('Activity 1', $project)
            ->willReturn($activity);

        $this->activityHistoryRepository
            ->expects($this->once())
            ->method('removeActivityHistory')
            ->with($activity);

        ($this->service)($this->dto); // no exception = success
        $this->assertTrue(true); // dummy assertion to confirm no exception was thrown
    }

    public function testThrowsExceptionIfProjectNotFound(): void
    {
        $this->projectRepository
            ->method('checkIfProjectExists')
            ->with('Project A')
            ->willReturn(false);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Project with name Project A was not found');

        ($this->service)($this->dto);
    }

    public function testThrowsExceptionIfActivityNotFound(): void
    {
        $project = $this->createMock(Project::class);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);

        $this->activityHistoryRepository
            ->method('checkIfActivityHistoryFromProjectExists')
            ->with('Activity 1', $project)
            ->willReturn(false);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('An activity with this name (Activity 1) in project Project A was not found');

        ($this->service)($this->dto);
    }
}
