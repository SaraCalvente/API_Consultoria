<?php

declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\Client\Application\ClientDeleteByUserService;
use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\User;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteByUserServiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testInvokeDeletesClientIfNoProjects(): void
    {
        $user = $this->createMock(User::class);
        $client = $this->createMock(Client::class);

        $clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $clientRepository
            ->method('getClientByUser')
            ->with($user)
            ->willReturn($client);

        $projectRepository
            ->method('checkIfClientHasProjects')
            ->with($client)
            ->willReturn(null);

        $expectedResponse = new JsonResponse(['message' => 'Client deleted successfully']);

        $clientRepository
            ->method('deleteClient')
            ->with($client)
            ->willReturn($expectedResponse);

        $service = new ClientDeleteByUserService($clientRepository, $projectRepository);
        $response = $service($user);

        $this->assertEquals($expectedResponse->getContent(), $response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testInvokeReturnsProjectsIfClientHasProjects(): void
    {
        $user = $this->createMock(User::class);
        $client = $this->createMock(Client::class);
        $projectInfo = ['project1', 'project2'];

        $clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $clientRepository
            ->method('getClientByUser')
            ->willReturn($client);

        $projectRepository
            ->method('checkIfClientHasProjects')
            ->willReturn(new JsonResponse([
                'error' => 'Cannot delete client because there are associated projects.',
                'projects' => $projectInfo
            ], 402));

        $service = new ClientDeleteByUserService($clientRepository, $projectRepository);
        $response = $service($user);

        $expectedData = [
            'error' => 'Cannot delete client because there are associated projects.',
            'projects' => $projectInfo
        ];

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($expectedData, $responseData);
    }


}
