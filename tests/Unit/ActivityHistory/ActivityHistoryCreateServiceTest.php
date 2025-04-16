<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryCreateServiceTest extends TestCase
{
    private array $validData = [
        'name' => 'Implement login',
        'description' => 'Add login functionality',
        'date' => '2025-04-16',
        'projectName' => 'Awesome Project',
        'consultantEmail' => 'consultant@example.com',
    ];

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testActivityHistoryIsCreatedSuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $user = $this->createMock(User::class);

        $activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $projectRepository->method('checkIfProjectExists')->willReturn(true);
        $projectRepository->method('findProjectByName')->willReturn($project);
        $activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);
        $userRepository->method('findUserByEmail')->willReturn($user);
        $consultantRepository->method('checkIfConsultantExists')->willReturn(true);

        $project->method('getName')->willReturn('Awesome Project');

        $service = new ActivityHistoryCreateService(
            $activityHistoryRepository,
            $projectRepository,
            $consultantRepository,
            $userRepository
        );

        $response = $service->__invoke($this->validData);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('ActivityHistory created successfully', $response->getContent());
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns404IfProjectNotFound(): void
    {
        $activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $projectRepository->method('checkIfProjectExists')->willReturn(false);

        $service = new ActivityHistoryCreateService(
            $activityHistoryRepository,
            $projectRepository,
            $consultantRepository,
            $userRepository
        );

        $response = $service->__invoke($this->validData);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('Project with name', $response->getContent());
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns403IfActivityAlreadyExists(): void
    {
        $project = $this->createMock(Project::class);

        $activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        // Mocking the return for project existence
        $projectRepository->method('checkIfProjectExists')->willReturn(true);
        $projectRepository->method('findProjectByName')->willReturn($project);
        $activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(true);

        $service = new ActivityHistoryCreateService(
            $activityHistoryRepository,
            $projectRepository,
            $consultantRepository,
            $userRepository
        );

        $response = $service->__invoke($this->validData);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertStringContainsString('already exists', $response->getContent());
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testReturns404IfConsultantNotFound(): void
    {
        $project = $this->createMock(Project::class);
        $user = $this->createMock(User::class);

        $activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        // Mocking project and consultant existence
        $projectRepository->method('checkIfProjectExists')->willReturn(true);
        $projectRepository->method('findProjectByName')->willReturn($project);
        $activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);
        $userRepository->method('findUserByEmail')->willReturn($user);
        $consultantRepository->method('checkIfConsultantExists')->willReturn(false);

        $service = new ActivityHistoryCreateService(
            $activityHistoryRepository,
            $projectRepository,
            $consultantRepository,
            $userRepository
        );

        $response = $service->__invoke($this->validData);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('Consultant with name', $response->getContent());
    }
}
