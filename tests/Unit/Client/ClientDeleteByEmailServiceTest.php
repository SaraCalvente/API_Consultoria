<?php
declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\Client\Application\Admin\ClientDeleteByEmailService;
use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteByEmailServiceTest extends Unit
{
    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testInvokeDeletesClientIfNoProjectsExist(): void
    {
        $email = 'client@example.com';

        $user = $this->createMock(User::class);
        $client = $this->createMock(Client::class);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $userRepository->method('findUserByEmail')->with($email)->willReturn($user);
        $clientRepository->method('getClientByUser')->with($user)->willReturn($client);
        $projectRepository->method('checkIfClientHasProjects')->with($client)->willReturn(null);

        $clientRepository->method('deleteClient')->with($client)->willReturn(
            new JsonResponse(['message' => 'Client deleted successfully'], 200)
        );

        $service = new ClientDeleteByEmailService($clientRepository, $userRepository, $projectRepository);

        $response = $service(['email' => $email]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['message' => 'Client deleted successfully'], json_decode($response->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testInvokeReturnsResponseIfClientHasProjects(): void
    {
        $email = 'client@example.com';
        $user = $this->createMock(User::class);
        $client = $this->createMock(Client::class);
        $projectInfo = ['project1', 'project2'];


        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $userRepository->method('findUserByEmail')->willReturn($user);
        $clientRepository->method('getClientByUser')->willReturn($client);

        $mockResponse = new JsonResponse(['message' => 'Client has projects'], 402);
        $projectRepository->method('checkIfClientHasProjects')->willReturn(new JsonResponse([
            'error' => 'Cannot delete client because there are associated projects.',
            'projects' => $projectInfo
        ], 402));

        $service = new ClientDeleteByEmailService($clientRepository, $userRepository, $projectRepository);

        $response = $service(['email' => $email]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(402, $response->getStatusCode());
        $this->assertEquals([
            'error' => 'Cannot delete client because there are associated projects.',
            'projects' => $projectInfo
        ],
            json_decode($response->getContent(), true));
    }
}
