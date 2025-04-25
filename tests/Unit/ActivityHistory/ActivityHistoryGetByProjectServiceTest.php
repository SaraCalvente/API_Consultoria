<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Application\ActivityHistoryGetByProjectService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ActivityHistoryGetByProjectServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;

    private ActivityHistoryGetByProjectService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ActivityHistoryGetByProjectService($this->activityHistoryRepository, $this->projectRepository);
        $this->data = ['projectName' => 'Project A'];
    }

    /**
     * @throws Exception
     * @throws \DateMalformedStringException
     */
    public function testReturnsActivitiesByProjectSuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity1 = $this->createActivityMock(1, 'Activity 1', 'Description 1', '2025-04-16', $project, $user);
        $activity2 = $this->createActivityMock(2, 'Activity 2', 'Description 2', '2025-04-17', $project, $user);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);

        $this->activityHistoryRepository->method('findActivitiesHistoriesByProject')->with($project)->willReturn([$activity1, $activity2]);

        $response = ($this->service)($this->data);

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

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns404IfProjectNotFound(): void
    {

        $this->projectRepository->method('checkIfProjectExists')->willReturn(false);

        $response = ($this->service)($this->data);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('Project with name', $response->getContent());
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
