<?php

namespace App\Tests\Unit\Project;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Application\Project\ProjectCreateService;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Status;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\Client\Domain\Client;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\JsonResponse;
use PHPUnit\Framework\MockObject\Exception;

class ProjectCreateServiceTest extends Unit
{
    private ProjectRepositoryInterface $projectRepository;
    private UserRepositoryInterface $userRepository;
    private ClientRepositoryInterface $clientRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private ProjectCreateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->consultantRepository = $this->createMock(ConsultantRepositoryInterface::class);

        $this->service = new ProjectCreateService(
            $this->projectRepository,
            $this->userRepository,
            $this->clientRepository,
            $this->consultantRepository
        );
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testReturns403IfProjectAlreadyExists(): void
    {
        $this->projectRepository->method('checkIfProjectExists')->willReturn(true);

        $data = [
            'clientEmail' => 'client@example.com',
            'name' => 'MyProject',
            'description' => 'Test Desc',
            'startDate' => '2025-05-01',
            'endDate' => '2025-05-10',
            'status' => Status::PENDIENTE->value,
            'consultantsEmails' => [],
        ];

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertStringContainsString('already exists', $content['error']);
    }

    public function testReturns405IfDatesAreInvalid(): void
    {
        $this->projectRepository->method('checkIfProjectExists')->willReturn(false);
        $this->projectRepository->method('checkDates')->willReturn(false);

        $data = [
            'clientEmail' => 'client@example.com',
            'name' => 'MyProject',
            'description' => 'Test Desc',
            'startDate' => '2025-05-10',
            'endDate' => '2025-05-01',
            'status' => Status::PENDIENTE->value,
            'consultantsEmails' => [],
        ];

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(405, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertStringContainsString('Invalid date range', $content['error']);
    }

    public function testReturns201WhenProjectIsCreated(): void
    {
        $clientEmail = 'client@example.com';
        $consultantEmails = ['c1@example.com', 'c2@example.com'];

        $clientUser = $this->createMock(User::class);
        $client = $this->createMock(Client::class);
        $consultantUser = $this->createMock(User::class);
        $consultant = $this->createMock(Consultant::class);

        $this->projectRepository->method('checkIfProjectExists')->willReturn(false);
        $this->projectRepository->method('checkDates')->willReturn(true);

        $this->userRepository->method('findUserByEmail')->willReturnMap([
            [$clientEmail, $clientUser],
            [$consultantEmails[0], $consultantUser],
            [$consultantEmails[1], $consultantUser],
        ]);

        $this->clientRepository->method('findClientByUser')->willReturn($client);
        $this->consultantRepository->method('findConsultantByUser')->willReturn($consultant);
        $this->projectRepository->expects($this->once())->method('addProject');

        $data = [
            'clientEmail' => $clientEmail,
            'name' => 'New Project',
            'description' => 'Project Description',
            'startDate' => '2025-05-01',
            'endDate' => '2025-05-10',
            'status' => Status::PENDIENTE->value,
            'consultantsEmails' => $consultantEmails,
        ];

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Project created successfully', $content['message']);
        $this->assertArrayHasKey('project', $content);
    }

}
