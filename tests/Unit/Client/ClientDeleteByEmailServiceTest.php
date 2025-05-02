<?php
declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Client\Application\ClientDeleteByEmailService;
use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteByEmailServiceTest extends Unit
{
    private UserRepositoryInterface $userRepository;
    private ClientRepositoryInterface $clientRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ClientDeleteByEmailService $service;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ClientDeleteByEmailService($this->clientRepository, $this->userRepository, $this->projectRepository);
        $this->data = ['email' => 'client@example.com'];
    }
    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testInvokeDeletesClientIfNoProjectsExist(): void
    {

        $user = $this->createMock(User::class);
        $client = $this->createMock(Client::class);

        $this->userRepository->method('findUserByEmailOrFail')->with($this->data['email'])->willReturn($user);
        $this->clientRepository->method('findClientByUser')->with($user)->willReturn($client);
        $this->projectRepository->method('checkIfClientHasProjects')->with($client)->willReturn(null);

        $this->clientRepository->method('deleteClient')->with($client)->willReturn(
            new JsonResponse(['message' => 'Client deleted successfully'], 200)
        );

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Client deleted successfully'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testInvokeReturnsResponseIfClientHasProjects(): void
    {
        $user = $this->createMock(User::class);
        $client = $this->createMock(Client::class);
        $projectInfo = ['project1', 'project2'];

        $this->userRepository->method('findUserByEmailOrFail')->willReturn($user);
        $this->clientRepository->method('findClientByUser')->willReturn($client);

        $this->projectRepository->method('checkIfClientHasProjects')->willReturn(new JsonResponse([
            'error' => 'Cannot delete client because there are associated projects.',
            'projects' => $projectInfo
        ], 402));

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Cannot delete client because there are associated projects.',
            'projects' => $projectInfo
        ],
            json_decode($response->getContent(), true));
    }
}
