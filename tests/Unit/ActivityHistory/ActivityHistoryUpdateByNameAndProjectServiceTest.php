<?php

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryUpdateByNameAndProjectService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryUpdateDTO;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ActivityHistoryNotFromUserException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;

class ActivityHistoryUpdateByNameAndProjectServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private ActivityHistoryUpdateByNameAndProjectService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new ActivityHistoryUpdateByNameAndProjectService(
            $this->activityHistoryRepository,
            $this->projectRepository,
            $this->consultantRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testUpdatesActivityDescriptionSuccessfully(): void
    {
        $dto = new ActivityHistoryUpdateDTO( 'Activity 1', 'Project A',  'Updated description');

        $user = $this->createMock(User::class);
        $project = $this->createMock(Project::class);
        $activity = $this->createMock(ActivityHistory::class);
        $consultant = $this->createMock(Consultant::class);

        $this->projectRepository->method('findProjectByName')->with('Project A')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(true);
        $this->activityHistoryRepository->method('findActivityHistoryFromProject')->willReturn($activity);

        $this->consultantRepository->method('checkIfConsultantExists')->with($user)->willReturn(true);
        $this->consultantRepository->method('findConsultantByUser')->with($user)->willReturn($consultant);

        $activityHistories = new ArrayCollection([$activity]);
        $consultant->method('getUser')->willReturn($user);
        $user->method('getActivityHistories')->willReturn($activityHistories);

        $activity->expects($this->once())->method('setDescription')->with('Updated description');
        $this->activityHistoryRepository->expects($this->once())->method('saveActivityHistory');

        $activity->method('getId')->willReturn(1);
        $activity->method('getName')->willReturn('Activity 1');
        $activity->method('getDescription')->willReturn('Updated description');
        $activity->method('getDate')->willReturn(new \DateTime('2025-04-16'));
        $activity->method('getProject')->willReturn($project);
        $project->method('getId')->willReturn(300);
        $activity->method('getUser')->willReturn($user);
        $user->method('getId')->willReturn(200);

        $result = ($this->service)($user, $dto);

        $this->assertInstanceOf(ActivityHistoryDTO::class, $result);
        $this->assertEquals('Activity 1', $result->name);
        $this->assertEquals('Updated description', $result->description);
        $this->assertEquals(300, $result->projectId);
        $this->assertEquals('2025-04-16', $result->date);
        $this->assertEquals(200, $result->userId);
    }

    /**
     * @throws Exception
     */
    public function testThrowsProjectNotFoundException(): void
    {
        $this->expectException(ProjectNotFoundException::class);

        $dto = new ActivityHistoryUpdateDTO('Nonexistent Project', 'Activity 1', 'Updated description');
        $user = $this->createMock(User::class);

        $this->projectRepository->method('findProjectByName')->willReturn(null);

        ($this->service)($user, $dto);
    }

    /**
     * @throws Exception
     */
    public function testThrowsActivityHistoryNotFoundException(): void
    {
        $this->expectException(ActivityHistoryNotFoundException::class);

        $dto = new ActivityHistoryUpdateDTO('Project A', 'Missing Activity', 'Updated description');
        $user = $this->createMock(User::class);
        $project = $this->createMock(Project::class);

        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);

        ($this->service)($user, $dto);
    }

    /**
     * @throws Exception
     */
    public function testThrowsActivityHistoryNotFromUserException(): void
    {
        $this->expectException(ActivityHistoryNotFromUserException::class);

        $dto = new ActivityHistoryUpdateDTO('Project A', 'Activity 1', 'Updated description');
        $user = $this->createMock(User::class);
        $project = $this->createMock(Project::class);
        $activity = $this->createMock(ActivityHistory::class);
        $consultant = $this->createMock(Consultant::class);

        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(true);
        $this->activityHistoryRepository->method('findActivityHistoryFromProject')->willReturn($activity);

        $this->consultantRepository->method('checkIfConsultantExists')->willReturn(true);
        $this->consultantRepository->method('findConsultantByUser')->willReturn($consultant);

        $consultant->method('getUser')->willReturn($user);
        $user->method('getActivityHistories')->willReturn(new ArrayCollection([]));

        ($this->service)($user, $dto);
    }
}
