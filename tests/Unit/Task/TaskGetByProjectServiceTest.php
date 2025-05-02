<?php
declare(strict_types=1);

namespace App\Tests\Unit\Task;

use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Application\Task\TaskGetByProjectService;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Status;
use App\Project\Domain\Task\Task;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskGetByProjectServiceTest extends Unit
{
    private $taskRepository;
    private $projectRepository;
    private $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new TaskGetByProjectService(
            $this->taskRepository,
            $this->projectRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testFindTasksByProjectSuccessfully(): void
    {
        $data = [
            'projectName' => 'Project 1'
        ];

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
        $task2->method('getStatus')->willReturn(Status::COMPLETADO);
        $task2->method('getStartDate')->willReturn($startDate);
        $task2->method('getEndDate')->willReturn(new \DateTime('2024-05-01'));
        $task2->method('getProject')->willReturn($project);
        $task2->method('getConsultants')->willReturn(new ArrayCollection([$consultant]));

        $this->projectRepository
            ->expects($this->once())
            ->method('findProjectByName')
            ->with($data['projectName'])
            ->willReturn($project);

        $this->taskRepository
            ->expects($this->once())
            ->method('findTasksByProject')
            ->with($project)
            ->willReturn([$task1, $task2]);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $dataResponse = json_decode($response->getContent(), true);

        $this->assertEquals('Tasks retrieved successfully', $dataResponse['message']);
        $this->assertIsArray($dataResponse['tasks']);
        $this->assertCount(2, $dataResponse['tasks']);
    }

}
