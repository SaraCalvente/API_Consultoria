<?php
declare(strict_types=1);

namespace App\Tests\Unit\Client\Service;

use App\Client\Application\Admin\ClientUpdateByEmailService;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByEmailServiceTest extends TestCase
{
    /**
     * @throws NoDataToUpdateException
     * @throws Exception
     */
    public function testClientIsUpdatedSuccessfully(): void
    {
        $email = 'client@example.com';
        $address = 'Calle Actualizada 123';
        $phone = '666123456';
        $user = $this->createMock(User::class);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $clientRepository = $this->createMock(ClientRepositoryInterface::class);

        $userRepository->method('findUserByEmail')->with($email)->willReturn($user);

        $expectedResponse = new JsonResponse([
            'message' => 'Client updated successfully',
            'id' => 1,
            'user_id' => 1,
            'email' => $email,
            'name' => 'Ana',
            'surNames' => 'Garcia Ruiz',
            'address' => $address,
            'phone_number' => $phone,
            'roles' => 'ROLE_CLIENT'
        ], 200);

        $clientRepository->method('updateClient')->with($user, $address, $phone)->willReturn($expectedResponse);

        $service = new ClientUpdateByEmailService($clientRepository, $userRepository);

        $result = $service([
            'email' => $email,
            'address' => $address,
            'phoneNumber' => $phone,
        ]);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(json_decode($expectedResponse->getContent(), true), json_decode($result->getContent(), true));
    }

    /**
     * @throws Exception
     */
    public function testThrowsExceptionIfNoDataToUpdate(): void
    {
        $this->expectException(NoDataToUpdateException::class);

        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $clientRepository = $this->createMock(ClientRepositoryInterface::class);

        $user = $this->createMock(User::class);
        $userRepository->method('findUserByEmail')->willReturn($user);

        $service = new ClientUpdateByEmailService($clientRepository, $userRepository);

        $service([
            'email' => 'client@example.com',
        ]);
    }
}
