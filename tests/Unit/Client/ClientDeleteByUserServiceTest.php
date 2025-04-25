<?php

declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\Client\Application\ClientDeleteByEmailService;
use App\Client\Application\ClientDeleteByUserService;
use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteByUserServiceTest extends Unit
{
    private ClientRepositoryInterface $clientRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ClientDeleteByUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->projectRepository = $this->createMock(ProjectRepositoryInterface::class);

        $this->service = new ClientDeleteByUserService($this->clientRepository, $this->projectRepository);
    }
    /**
     * @throws Exception
     */
    public function testInvokeDeletesClientIfNoProjects(): void
    {
        $client = $this->createMock(Client::class);
        $user = $this->createMock(User::class);

        $this->clientRepository->method('findClientByUser')->with($user)
            ->willReturn($client);

        $this->projectRepository->method('checkIfClientHasProjects')->with($client)
            ->willReturn(null);

        $expectedResponse = new JsonResponse(['message' => 'Client deleted successfully']);

        $this->clientRepository->method('deleteClient')->with($client)
            ->willReturn($expectedResponse);

        $response = ($this->service)($user);

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

        $this->clientRepository->method('findClientByUser')->willReturn($client);

        $this->projectRepository->method('checkIfClientHasProjects')
            ->willReturn(new JsonResponse([
                'error' => 'Cannot delete client because there are associated projects.',
                'projects' => $projectInfo
            ], 402));

        $response = ($this->service)($user);

        $expectedData = [
            'error' => 'Cannot delete client because there are associated projects.',
            'projects' => $projectInfo
        ];

        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($expectedData, $responseData);
    }


}
