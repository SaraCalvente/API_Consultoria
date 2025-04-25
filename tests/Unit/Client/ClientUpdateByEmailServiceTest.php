<?php
declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\Client\Application\ClientGetByUserService;
use App\Client\Application\ClientUpdateByEmailService;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByEmailServiceTest extends Unit
{
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;
    private ClientUpdateByEmailService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new ClientUpdateByEmailService($this->clientRepository, $this->userRepository);
    }
    /**
     * @throws NoDataToUpdateException
     * @throws Exception
     */
    public function testClientIsUpdatedSuccessfully(): void
    {

        $email = 'client@example.com';
        $address = 'Calle Actualizada 123';
        $phone = '666123456';

        $data = [
            'email' => $email,
            'address' => $address,
            'phoneNumber' => $phone
        ];

        $user = $this->createMock(User::class);

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);

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

        $this->clientRepository->method('updateClient')->with($user, $address, $phone)->willReturn($expectedResponse);

        $result = ($this->service)($data);
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(json_decode($expectedResponse->getContent(), true), json_decode($result->getContent(), true));
    }

    /**
     * @throws NoDataToUpdateException
     * @throws Exception
     */
    public function testClientAddressIsUpdatedSuccessfully(): void
    {

        $email = 'client@example.com';
        $address = 'Calle Actualizada 123';

        $data = [
            'email' => $email,
            'address' => $address,
        ];

        $user = $this->createMock(User::class);

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);

        $expectedResponse = new JsonResponse([
            'message' => 'Client updated successfully',
            'id' => 1,
            'user_id' => 1,
            'email' => $email,
            'name' => 'Ana',
            'surNames' => 'Garcia Ruiz',
            'address' => $address,
            'phone_number' => '',
            'roles' => 'ROLE_CLIENT'
        ], 200);

        $this->clientRepository->method('updateClient')->with($user, $address)->willReturn($expectedResponse);

        $result = ($this->service)($data);
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(json_decode($expectedResponse->getContent(), true), json_decode($result->getContent(), true));
    }

    /**
     * @throws NoDataToUpdateException
     * @throws Exception
     */
    public function testClientPhoneIsUpdatedSuccessfully(): void
    {

        $email = 'client@example.com';
        $phone = '666123456';

        $data = [
            'email' => $email,
            'phoneNumber' => $phone,
        ];

        $user = $this->createMock(User::class);

        $this->userRepository->method('findUserByEmail')->with($email)->willReturn($user);

        $expectedResponse = new JsonResponse([
            'message' => 'Client updated successfully',
            'id' => 1,
            'user_id' => 1,
            'email' => $email,
            'name' => 'Ana',
            'surNames' => 'Garcia Ruiz',
            'address' => '',
            'phone_number' => $phone,
            'roles' => 'ROLE_CLIENT'
        ], 200);

        $this->clientRepository->method('updateClient')->with($user, null, $phone)->willReturn($expectedResponse);

        $result = ($this->service)($data);
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

        $user = $this->createMock(User::class);
        $this->userRepository->method('findUserByEmail')->willReturn($user);

        $data = ['email' => 'client@example.com'];
        ($this->service)($data);
    }
}
