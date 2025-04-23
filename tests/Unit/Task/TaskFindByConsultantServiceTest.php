<?php
declare(strict_types=1);

namespace App\Tests\Unit\Project\Application\Task;

use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Application\Task\TaskFindByConsultantService;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use App\Project\Domain\Task\Task;
use App\User\Domain\User;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindByConsultantServiceTest extends Unit
{
    private $consultantRepository;
    private $taskRepository;
    private $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);

        $this->service = new TaskFindByConsultantService(
            $this->consultantRepository,
            $this->taskRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testFindTasksByConsultant(): void
    {
        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(2);

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(99);

        $startDate = new \DateTime('2024-04-01');

        $task1 = $this->createMock(Task::class);
        $task1->method('getId')->willReturn(1);
        $task1->method('getName')->willReturn('Task 1');
        $task1->method('getDescription')->willReturn('Description 1');
        $task1->method('getStatus')->willReturn(Status::PENDIENTE);
        $task1->method('getStartDate')->willReturn($startDate);
        $task1->method('getEndDate')->willReturn(null);
        $task1->method('getProject')->willReturn($project);
        $task1->method('getConsultants')->willReturn(new ArrayCollection([$consultant]));

        $task2 = $this->createMock(Task::class);
        $task2->method('getId')->willReturn(2);
        $task2->method('getName')->willReturn('Task 2');
        $task2->method('getDescription')->willReturn('Description 2');
        $task2->method('getStatus')->willReturn(Status::EN_PROCESO);
        $task2->method('getStartDate')->willReturn($startDate);
        $task2->method('getEndDate')->willReturn(null);
        $task2->method('getProject')->willReturn($project);
        $task2->method('getConsultants')->willReturn(new ArrayCollection([$consultant]));

        $this->consultantRepository
            ->expects($this->once())
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $this->taskRepository
            ->expects($this->once())
            ->method('findTaskByConsultant')
            ->with($consultant)
            ->willReturn([$task1, $task2]);

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Tasks retrieved successfully', $data['message']);
        $this->assertEquals(2, $data['current_consultant_id']);
        $this->assertIsArray($data['tasks']);
        $this->assertCount(2, $data['tasks']);
    }

}
