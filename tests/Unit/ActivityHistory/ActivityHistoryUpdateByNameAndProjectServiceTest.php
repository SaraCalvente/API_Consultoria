<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Application\ActivityHistoryUpdateByNameAndProjectService;
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

class ActivityHistoryUpdateByNameAndProjectServiceTest extends Unit
{

    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;

    private ActivityHistoryUpdateByNameAndProjectService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new ActivityHistoryUpdateByNameAndProjectService($this->activityHistoryRepository, $this->projectRepository, $this->consultantRepository);
        $this->data = [
            'projectName' => 'Project A',
            'name' => 'Activity 1',
            'description' => 'New updated description'
        ];
    }
    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testUpdatesActivityDescriptionSuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(300);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(200);

        $activity = $this->createActivityMock(1, 'Activity 1', 'New updated description', '2025-04-16', $project, $user);

        $activity->expects($this->once())
            ->method('setDescription')
            ->with('New updated description');

        $this->projectRepository->method('findProjectByName')->with('Project A')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')
            ->with('Activity 1', $project)
            ->willReturn(true);

        $this->activityHistoryRepository->method('findActivityHistoryFromProject')
            ->with('Activity 1', $project)
            ->willReturn($activity);

        $this->activityHistoryRepository->expects($this->once())->method('saveActivityHistory');

        $response = ($this->service)($user, $this->data);

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
