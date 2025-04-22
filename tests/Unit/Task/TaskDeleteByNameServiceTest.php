<?php

namespace App\Tests\Unit\Project;

use App\Project\Application\Task\TaskDeleteByNameService;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Task\Task;
use App\Consultant\Domain\Consultant\Consultant;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskDeleteByNameServiceTest extends Unit
{
    private TaskRepositoryInterface $taskRepository;
    private ProjectRepositoryInterface $projectRepository;
    private TaskDeleteByNameService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new TaskDeleteByNameService(
            $this->taskRepository,
            $this->projectRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testDeleteTaskSuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $task = $this->createMock(Task::class);
        $consultant1 = $this->createMock(Consultant::class);
        $consultant2 = $this->createMock(Consultant::class);

        $task->method('getConsultants')->willReturn(new ArrayCollection([$consultant1, $consultant2]));

        $task->expects($this->exactly(2))->method('removeConsultant');
        $this->taskRepository->expects($this->once())->method('removeTask')->with($task);

        $this->projectRepository
            ->method('findProjectByName')
            ->with('DemoProject')
            ->willReturn($project);

        $this->taskRepository
            ->method('findTaskFromProject')
            ->with('TaskToDelete', $project)
            ->willReturn($task);

        $response = ($this->service)('TaskToDelete', 'DemoProject', [
            'name' => 'TaskToDelete',
            'projectName' => 'DemoProject'
        ]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Task deleted successfully'], json_decode($response->getContent(), true));
    }
}
