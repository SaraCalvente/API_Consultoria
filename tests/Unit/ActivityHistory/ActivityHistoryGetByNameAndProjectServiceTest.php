<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByNameAndProjectService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryByNameAndProjectDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;

class ActivityHistoryGetByNameAndProjectServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ActivityHistoryGetByNameAndProjectService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ActivityHistoryGetByNameAndProjectService(
            $this->activityHistoryRepository,
            $this->projectRepository
        );
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testReturnsDTOWhenActivityIsFound(): void
    {
        $dto = new ActivityHistoryByNameAndProjectDTO('Project A', 'Activity 1');

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity = $this->createActivityMock(1, 'Activity 1', 'Description', '2025-04-16', $project, $user);

        $this->projectRepository
            ->method('findProjectByName')
            ->with('Project A')
            ->willReturn($project);

        $this->activityHistoryRepository
            ->method('checkIfActivityHistoryFromProjectExists')
            ->with('Activity 1', $project)
            ->willReturn(true);

        $this->activityHistoryRepository
            ->method('findActivityHistoryFromProject')
            ->with('Activity 1', $project)
            ->willReturn($activity);

        $result = ($this->service)($dto);

        $this->assertInstanceOf(ActivityHistoryDTO::class, $result);
        $this->assertEquals('Activity 1', $result->name);
        $this->assertEquals('Description', $result->description);
        $this->assertEquals(300, $result->projectId);
        $this->assertEquals('2025-04-16', $result->date);
        $this->assertEquals(200, $result->userId);
    }

    public function testThrowsExceptionWhenProjectNotFound(): void
    {
        $this->expectException(ProjectNotFoundException::class);

        $dto = new ActivityHistoryByNameAndProjectDTO('Activity 1', 'Nonexistent Project');

        $this->projectRepository->method('findProjectByName')->willReturn(null);

        ($this->service)($dto);
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionWhenActivityNotFound(): void
    {
        $this->expectException(ActivityHistoryNotFoundException::class);

        $dto = new ActivityHistoryByNameAndProjectDTO( 'Project A', 'Missing Activity');
        $project = $this->createMock(Project::class);

        $this->projectRepository
            ->method('findProjectByName')
            ->with('Project A')
            ->willReturn($project);

        $this->activityHistoryRepository
            ->method('checkIfActivityHistoryFromProjectExists')
            ->with('Missing Activity', $project)
            ->willReturn(false);

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
