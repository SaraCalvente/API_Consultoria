<?php

namespace App\Tests\Unit\Task;

use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Application\Task\TaskFindAllService;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use App\Project\Domain\Task\Task;
use App\Project\Domain\Task\TaskDTO;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskFindAllServiceTest extends Unit
{
    private TaskRepositoryInterface $taskRepository;
    private TaskFindAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->service = new TaskFindAllService($this->taskRepository);
    }

    /**
     * @throws Exception
     */
    public function testFindAllTasksSuccessfully(): void
    {
        // Mock de Project con ID
        $projectMock = $this->createMock(Project::class);
        $projectMock->method('getId')->willReturn(123);

        // Mock de Consultant
        $consultantMock = $this->createMock(Consultant::class);
        $consultantMock->method('getId')->willReturn(456);

        // Mock de Task
        $taskMock = $this->createMock(Task::class);
        $taskMock->method('getId')->willReturn(1);
        $taskMock->method('getName')->willReturn('Test Task');
        $taskMock->method('getDescription')->willReturn('Test description');
        $taskMock->method('getProject')->willReturn($projectMock);
        $taskMock->method('getStartDate')->willReturn(new \DateTime('2025-01-01'));
        $taskMock->method('getEndDate')->willReturn(new \DateTime('2025-01-31'));
        $taskMock->method('getStatus')->willReturn(Status::PENDIENTE);
        $consultantCollectionMock = new ArrayCollection([$consultantMock]);
        $taskMock->method('getConsultants')->willReturn($consultantCollectionMock);

        $this->taskRepository
            ->method('findAllTasks')
            ->willReturn([$taskMock]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Tasks retrieved successfully', $data['message']);
        $this->assertCount(1, $data['tasks']);
        $this->assertEquals(1, $data['tasks'][0]['task_id']);
        $this->assertEquals('Test Task', $data['tasks'][0]['name']);
        $this->assertEquals(123, $data['tasks'][0]['project_id']);
        $this->assertEquals([456], $data['tasks'][0]['consultantsId']);
    }


    public function testFindAllTasksReturnsNotFoundWhenEmpty(): void
    {
        $this->taskRepository
            ->method('findAllTasks')
            ->willReturn([]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(['error' => 'There are no tasks'], json_decode($response->getContent(), true));
    }
}
