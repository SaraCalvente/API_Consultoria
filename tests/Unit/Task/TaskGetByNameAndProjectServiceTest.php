<?php
declare(strict_types=1);

namespace App\Tests\Unit\Task;

use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Application\Task\TaskGetByNameAndProjectService;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Status;
use App\Project\Domain\Task\Task;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskGetByNameAndProjectServiceTest extends Unit
{
    private $taskRepository;
    private $projectRepository;
    private $service;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new TaskGetByNameAndProjectService(
            $this->taskRepository,
            $this->projectRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testFindTaskByNameAndProjectSuccessfully(): void
    {
        $data = [
            'name' => 'Task 1',
            'projectName' => 'Project 1'
        ];

        $consultant = $this->createMock(Consultant::class);
        $consultant->method('getId')->willReturn(2);


        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(99);

        $startDate = new \DateTime('2024-04-01');

        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(1);
        $task->method('getName')->willReturn('Task 1');
        $task->method('getDescription')->willReturn('Description 1');
        $task->method('getStatus')->willReturn(Status::PENDIENTE);
        $task->method('getStartDate')->willReturn($startDate);
        $task->method('getEndDate')->willReturn(null);
        $task->method('getProject')->willReturn($project);
        $task->method('getConsultants')->willReturn(new ArrayCollection([$consultant]));
        $this->projectRepository
            ->expects($this->once())
            ->method('findProjectByName')
            ->with($data['projectName'])
            ->willReturn($project);

        $this->taskRepository
            ->expects($this->once())
            ->method('checkIfTaskFromProjectExists')
            ->with($data['name'], $project)
            ->willReturn(true);

        $this->taskRepository
            ->expects($this->once())
            ->method('findTaskFromProject')
            ->with($data['name'], $project)
            ->willReturn($task);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $dataResponse = json_decode($response->getContent(), true);

        $this->assertEquals('Tasks retrieved successfully', $dataResponse['message']);
        $this->assertIsArray($dataResponse['task']);
    }

    public function testProjectNotFoundException(): void
    {
        $this->expectException(ProjectNotFoundException::class);

        $data = [
            'name' => 'Task 1',
            'projectName' => 'Project 1'
        ];

        $this->projectRepository
            ->expects($this->once())
            ->method('findProjectByName')
            ->with($data['projectName'])
            ->willReturn(null); // Simulate project not found

        ($this->service)($data);
    }

    /**
     * @throws Exception
     */
    public function testTaskNotFoundException(): void
    {
        $this->expectException(TaskNotFoundException::class);

        $data = [
            'name' => 'Task 1',
            'projectName' => 'Project 1'
        ];

        $project = $this->createMock(Project::class);

        $this->projectRepository
            ->expects($this->once())
            ->method('findProjectByName')
            ->with($data['projectName'])
            ->willReturn($project);

        $this->taskRepository
            ->expects($this->once())
            ->method('checkIfTaskFromProjectExists')
            ->with($data['name'], $project)
            ->willReturn(false);

        ($this->service)($data);
    }
}
