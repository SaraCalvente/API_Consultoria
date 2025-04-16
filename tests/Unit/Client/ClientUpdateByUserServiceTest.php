<?php

declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\Client\Application\ClientUpdateByUserService;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\User;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByUserServiceTest extends TestCase
{
    private ClientRepositoryInterface $clientRepository;
    private ClientUpdateByUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->service = new ClientUpdateByUserService($this->clientRepository);
    }

    /**
     * @throws NoDataToUpdateException
     * @throws Exception
     */
    public function testClientUpdateSuccess(): void
    {
        $user = $this->createMock(User::class);
        $data = ['address' => 'C/ Example 10', 'phoneNumber' => '123456789'];

        $expectedResponse = new JsonResponse(['message' => 'Client updated successfully']);

        $this->clientRepository
            ->expects($this->once())
            ->method('updateClient')
            ->with($user, 'C/ Example 10', '123456789')
            ->willReturn($expectedResponse);

        $response = ($this->service)($user, $data);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @throws Exception
     * @throws NoDataToUpdateException
     */
    public function testClientUpdateWithOnlyPhoneNumber(): void
    {
        $user = $this->createMock(User::class);
        $data = ['phoneNumber' => '123456789'];

        $expectedResponse = new JsonResponse(['message' => 'Client updated successfully']);

        $this->clientRepository
            ->expects($this->once())
            ->method('updateClient')
            ->with($user, null, '123456789')
            ->willReturn($expectedResponse);

        $response = ($this->service)($user, $data);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @throws NoDataToUpdateException
     * @throws Exception
     */
    public function testClientUpdateWithOnlyAddress(): void
    {
        $user = $this->createMock(User::class);
        $data = ['address' => 'C/ SoloDireccion'];

        $expectedResponse = new JsonResponse(['message' => 'Client updated successfully']);

        $this->clientRepository
            ->expects($this->once())
            ->method('updateClient')
            ->with($user, 'C/ SoloDireccion', null)
            ->willReturn($expectedResponse);

        $response = ($this->service)($user, $data);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * @throws Exception
     */
    public function testClientUpdateThrowsExceptionIfNoData(): void
    {
        $this->expectException(NoDataToUpdateException::class);

        $user = $this->createMock(User::class);
        $data = [];

        ($this->service)($user, $data);
    }
}
