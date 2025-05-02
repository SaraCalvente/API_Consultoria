<?php

namespace App\Tests\Unit\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Application\Task\TaskCreateSevice;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Status;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskCreateServiceTest extends Unit
{
    private ProjectRepositoryInterface $projectRepository;
    private TaskRepositoryInterface $taskRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private TaskCreateSevice $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new TaskCreateSevice(
            $this->taskRepository,
            $this->projectRepository,
            $this->consultantRepository,
            $this->userRepository
        );
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testTaskCreatedSuccessfully(): void
    {
        $data = [
            'projectName' => 'TestProject',
            'name' => 'Task 1',
            'description' => 'This is a test task',
            'startDate' => '2025-05-01',
            'endDate' => '2025-05-15',
            'status' => Status::EN_PROCESO->value,
            'consultantsEmails' => ['consultant@example.com'],
        ];

        $project = $this->createMock(Project::class);
        $consultantUser = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $consultant->method('getId')->willReturn(10);
        $project->method('getId')->willReturn(5);

        $this->projectRepository->method('checkIfProjectExists')->with('TestProject')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->with('TestProject')->willReturn($project);

        $this->taskRepository->method('checkIfTaskFromProjectExists')->with('Task 1', $project)->willReturn(false);
        $this->taskRepository->method('checkDates')->with('2025-05-01', '2025-05-15')->willReturn(true);
        $this->taskRepository->expects($this->once())->method('addTask');

        $this->userRepository->method('findUserByEmailOrFail')->with('consultant@example.com')->willReturn($consultantUser);
        $this->consultantRepository->method('findConsultantByUser')->with($consultantUser)->willReturn($consultant);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Task created successfully', $content['message']);
        $this->assertArrayHasKey('task', $content);
        $this->assertEquals(5, $content['task']['project_id']);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testProjectNotFound(): void
    {
        $this->projectRepository->method('checkIfProjectExists')->willReturn(false);

        $response = ($this->service)([
            'projectName' => 'InvalidProject',
            'name' => 'Task X',
            'description' => '...',
            'startDate' => '2025-01-01',
            'endDate' => '2025-01-02',
            'status' => Status::EN_PROCESO->value,
            'consultantsEmails' => [],
        ]);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('was not found', $response->getContent());
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testTaskAlreadyExists(): void
    {
        $project = $this->createMock(Project::class);
        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->taskRepository->method('checkIfTaskFromProjectExists')->willReturn(true);

        $response = ($this->service)([
            'projectName' => 'TestProject',
            'name' => 'ExistingTask',
            'description' => '...',
            'startDate' => '2025-01-01',
            'endDate' => '2025-01-02',
            'status' => Status::EN_PROCESO->value,
            'consultantsEmails' => [],
        ]);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('already exists', $response->getContent());
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testInvalidDates(): void
    {
        $project = $this->createMock(Project::class);
        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->taskRepository->method('checkIfTaskFromProjectExists')->willReturn(false);
        $this->taskRepository->method('checkDates')->willReturn(false);

        $response = ($this->service)([
            'projectName' => 'TestProject',
            'name' => 'TaskWithBadDates',
            'description' => '...',
            'startDate' => 'invalid',
            'endDate' => 'also invalid',
            'status' => Status::EN_PROCESO->value,
            'consultantsEmails' => [],
        ]);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertStringContainsString('Invalid date range', $response->getContent());
    }
}
