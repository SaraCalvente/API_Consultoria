<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Application\ActivityHistoryGetByNameAndProjectService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryGetByNameAndProjectServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;

    private ActivityHistoryGetByNameAndProjectService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ActivityHistoryGetByNameAndProjectService($this->activityHistoryRepository, $this->projectRepository);
        $this->data = [
            'name' => 'Activity 1',
            'projectName' => 'Project A'
        ];
    }

    /**
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    public function testReturns201WhenActivityIsFound(): void
    {
        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity = $this->createActivityMock(1, 'Activity 1', 'Description', '2025-04-16', $project, $user);

        $this->projectRepository->method('findProjectByName')->with('Project A')->willReturn($project);

        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->with('Activity 1', $project)->willReturn(true);
        $this->activityHistoryRepository->method('findActivityHistoryFromProject')->with('Activity 1', $project)->willReturn($activity);

        $response = ($this->service)($this->data);

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

        $this->projectRepository->method('findProjectByName')->willReturn(null);

        ($this->service)($this->data);
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionWhenActivityNotFound(): void
    {
        $this->expectException(ActivityHistoryNotFoundException::class);

        $project = $this->createMock(Project::class);

        $this->projectRepository->method('findProjectByName')->with('Project A')->willReturn($project);

        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);

        ($this->service)($this->data);
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
