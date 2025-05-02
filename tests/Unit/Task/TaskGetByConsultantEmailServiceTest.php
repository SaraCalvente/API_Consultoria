<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task;

use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Application\Task\TaskGetByConsultantEmailService;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use App\Project\Domain\Task\Task;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskGetByConsultantEmailServiceTest extends Unit
{
    private TaskRepositoryInterface $taskRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private TaskGetByConsultantEmailService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new TaskGetByConsultantEmailService(
            $this->taskRepository,
            $this->consultantRepository,
            $this->userRepository
        );
    }

    public function testInvokeReturnsTasksSuccessfully(): void
    {
        $email = 'consultant@example.com';
        $user = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);
        $task = $this->createMock(Task::class);
        $project = $this->createMock(Project::class);

        // Mock project ID
        $project->method('getId')->willReturn(10);

        // Mock consultants list
        $consultant1 = $this->createMock(Consultant::class);
        $consultant2 = $this->createMock(Consultant::class);
        $consultant1->method('getId')->willReturn(101);
        $consultant2->method('getId')->willReturn(102);
        $consultants = new ArrayCollection([$consultant1, $consultant2]);

        // Mock Task methods
        $task->method('getId')->willReturn(1);
        $task->method('getName')->willReturn('Task Name');
        $task->method('getDescription')->willReturn('Task Description');
        $task->method('getProject')->willReturn($project);
        $task->method('getStartDate')->willReturn(new \DateTime('2025-01-01'));
        $task->method('getEndDate')->willReturn(new \DateTime('2025-01-31'));
        $task->method('getStatus')->willReturn(Status::COMPLETADO);
        $task->method('getConsultants')->willReturn($consultants);

        $this->userRepository
            ->method('findUserByEmailOrFail')
            ->with($email)
            ->willReturn($user);

        $this->consultantRepository
            ->method('findConsultantByUser')
            ->with($user)
            ->willReturn($consultant);

        $this->taskRepository
            ->method('findTaskByConsultant')
            ->with($consultant)
            ->willReturn([$task]);

        $response = ($this->service)(['email' => $email]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Tasks retrieved successfully', $data['message']);
        $this->assertCount(1, $data['tasks']);
        $this->assertEquals([
            'task_id' => 1,
            'name' => 'Task Name',
            'description' => 'Task Description',
            'project_id' => 10,
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'status' => 'Completado',
            'consultantsId' => [101, 102],
        ], $data['tasks'][0]);
    }

}
