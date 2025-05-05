<?php

declare(strict_types=1);

namespace App\Tests\Unit\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\DTO\ActivityHistoryCreateDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\User\Application\User\UserFindAllService;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use DomainException;
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
    private ActivityHistoryCreateDTO $dto;

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
        $this->dto = new ActivityHistoryCreateDTO(
            'Awesome Project',
            'Implement login',
            'Add login functionality',
            '2025-04-16',
            'consultant@example.com'
        );

    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     * @throws \Exception
     */
    public function testActivityHistoryIsCreatedSuccessfully(): void
    {
        $project = $this->createMock(Project::class);
        $user = $this->createMock(User::class);
        $activityHistory = $this->createMock(ActivityHistory::class);

        $project->method('getId')->willReturn(1);
        $user->method('getId')->willReturn(1);

        $date = new \DateTime('2025-04-16');

        $activityHistory->method('getName')->willReturn('Implement login');
        $activityHistory->method('getDescription')->willReturn('Add login functionality');
        $activityHistory->method('getProject')->willReturn($project);
        $activityHistory->method('getUser')->willReturn($user);
        $activityHistory->method('getDate')->willReturn($date);


        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);
        $this->userRepository->method('findUserByEmailOrFail')->willReturn($user);
        $this->consultantRepository->method('checkIfConsultantExists')->willReturn(true);
        $dto = ($this->service)($this->dto);

        $this->assertEquals('Implement login', $dto->name);
        $this->assertEquals('Add login functionality', $dto->description);
    }


    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     * @throws \Exception
     */
    public function testThrowsExceptionIfProjectNotFound(): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Project with name');

        $this->projectRepository->method('checkIfProjectExists')->willReturn(false);

        ($this->service)($this->dto);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     * @throws \Exception
     */
    public function testThrowsExceptionIfActivityAlreadyExists(): void
    {
        $project = $this->createMock(Project::class);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(true);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("already exists");

        ($this->service)($this->dto);
    }


    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     * @throws \Exception
     */
    public function testThrowsExceptionIfConsultantNotFound(): void
    {
        $project = $this->createMock(Project::class);
        $user = $this->createMock(User::class);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);
        $this->projectRepository->method('findProjectByName')->willReturn($project);
        $this->activityHistoryRepository->method('checkIfActivityHistoryFromProjectExists')->willReturn(false);
        $this->userRepository->method('findUserByEmailOrFail')->willReturn($user);
        $this->consultantRepository->method('checkIfConsultantExists')->willReturn(false);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Consultant with email");

        ($this->service)($this->dto);
    }
}
