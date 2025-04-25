<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Application\User\UserFindAllService;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryCreateServiceTest extends Unit
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;
    private UserRepositoryInterface $userRepository;
    private ConsultantRepositoryInterface $consultantRepository;

    private ActivityHistoryCreateService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->activityHistoryRepository = $this->createMock(ActivityHistoryRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new ActivityHistoryCreateService($this->activityHistoryRepository, $this->projectRepository, $this->consultantRepository, $this->userRepository);
        $this->data = [
            'name' => 'Implement login',
            'description' => 'Add login functionality',
            'date' => '2025-04-16',
            'projectName' => 'Awesome Project',
            'consultantEmail' => 'consultant@example.com',
        ];
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testActivityHistoryIsCreatedSuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $user = $this->createMock(User::class);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);
        $this->userRepository->method('findUserByEmail')->willReturn($user);
        $this->consultantRepository->method('checkIfConsultantExists')->willReturn(true);

        $project->method('getName')->willReturn('Awesome Project');


        $response = ($this->service)($this->data);

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

        $this->projectRepository->method('checkIfProjectExists')->willReturn(false);

        $response = ($this->service)($this->data);

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

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(true);

        $response = ($this->service)($this->data);

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

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);
        $this->userRepository->method('findUserByEmail')->willReturn($user);
        $this->consultantRepository->method('checkIfConsultantExists')->willReturn(false);

        $response = ($this->service)($this->data);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString('Consultant with name', $response->getContent());
    }
}
