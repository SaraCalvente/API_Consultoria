<?php
declare(strict_types=1);

namespace App\Tests\Unit\Task;

use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Application\Task\TaskUpdateByNameAndProjectService;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Task\Task;
use App\Project\Domain\Status;
use App\Project\Domain\Project\Project;
use App\User\Domain\Model\UserRepositoryInterface;
use App\Shared\Domain\Exception\TaskNotFoundException;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskUpdateByNameAndProjectServiceTest extends Unit
{
    private $taskRepository;
    private $projectRepository;
    private $userRepository;
    private $consultantRepository;
    private $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new TaskUpdateByNameAndProjectService(
            $this->taskRepository,
            $this->projectRepository,
            $this->userRepository,
            $this->consultantRepository
        );
    }

    /**
     * @throws \Exception
     * @throws Exception
     */
    public function testUpdateTaskSuccessfully(): void
    {
        $data = [
            'name' => 'Task 1',
            'projectName' => 'Project 1',
            'description' => 'Updated description',
            'status' => Status::PENDIENTE->value,
            'endDate' => '2024-04-10',
            'addConsultantsEmails' => ['consultant1@example.com'],
            'erraseConsultantsEmails' => ['consultant2@example.com'],
        ];

        $consultant1 = $this->createMock(Consultant::class);
        $consultant1->method('getId')->willReturn(2);

        $consultant2 = $this->createMock(Consultant::class);
        $consultant2->method('getId')->willReturn(3);

        $user1 = $this->createMock(User::class);
        $user1->method('getEmail')->willReturn(new EmailValueObject('consultant1@example.com'));

        $user2 = $this->createMock(User::class);
        $user2->method('getEmail')->willReturn(new EmailValueObject('consultant2@example.com'));

        $project = $this->createMock(Project::class);
        $project->method('getId')->willReturn(99);
        $project->method('getEndDate')->willReturn(new \DateTime('2024-05-01'));

        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(1);
        $task->method('getName')->willReturn('Task 1');
        $task->method('getStatus')->willReturn(Status::PENDIENTE);
        $task->method('getStartDate')->willReturn(new \DateTime('2024-04-01'));
        $task->method('getEndDate')->willReturn(null);
        $task->method('getProject')->willReturn($project);
        $task->method('getConsultants')->willReturn(new ArrayCollection([$consultant1]));

        $this->projectRepository
            ->expects($this->once())
            ->method('findProjectByName')
            ->with($data['projectName'])
            ->willReturn($project);

        $this->taskRepository
            ->expects($this->once())
            ->method('findTaskFromProject')
            ->with($data['name'], $project)
            ->willReturn($task);

        $this->taskRepository
            ->expects($this->any())
            ->method('checkDates')
            ->with($task->getStartDate()->format('Y-m-d'), $data['endDate'])
            ->willReturn(true);


        $this->userRepository
            ->expects($this->exactly(2))
            ->method('findUserByEmailOrFail')
            ->willReturnCallback(function (string $email) use ($user1, $user2) {
                return match ($email) {
                    'consultant1@example.com' => $user1,
                    'consultant2@example.com' => $user2,
                    default => null,
                };
            });

        $this->consultantRepository
            ->expects($this->exactly(2))
            ->method('findConsultantByUser')
            ->willReturnCallback(function (User $user) use ($user1, $user2, $consultant1, $consultant2) {
                return $user === $user1 ? $consultant1 : $consultant2;
            });

        $this->taskRepository
            ->expects($this->once())
            ->method('saveTask');

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $dataResponse = json_decode($response->getContent(), true);

        $this->assertEquals('Task updated successfully', $dataResponse['message']);
        $this->assertIsArray($dataResponse['project']);
    }


}
