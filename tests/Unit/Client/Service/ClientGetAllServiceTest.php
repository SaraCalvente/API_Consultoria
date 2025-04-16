<?php
declare(strict_types=1);

namespace App\Tests\Unit\Client\Application\Admin;

use App\Client\Application\Admin\ClientFindAllService;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Client\Domain\Client;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientFindAllServiceTest extends TestCase
{
    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testInvokeReturnsAllClientsAsJsonResponse(): void
    {
        $user1 = $this->createUserMock(1, 'user1@example.com', ['ROLE_CLIENT']);
        $user2 = $this->createUserMock(2, 'user2@example.com', ['ROLE_CLIENT']);

        $client1 = $this->createClientMock(10, $user1, 'Ana', 'García Ruiz', 'Calle Ejemplo, 9', '666666666');
        $client2 = $this->createClientMock(11, $user2, 'Miguel', 'García Ruiz', 'Calle Ejemplo, 10', '999999999');


        $clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $clientRepository->method('findAllClients')->willReturn([$client1, $client2]);

        $service = new ClientFindAllService($clientRepository);
        $response = $service();

        $this->assertInstanceOf(JsonResponse::class, $response);

        $expectedData = [
            [
                'client_id' => 10,
                'user_id' => 1,
                'email' => 'user1@example.com',
                'name' => 'Ana',
                'surnames' => 'García Ruiz',
                'address' => 'Calle Ejemplo, 9',
                'phone_number' => '666666666',
                'roles' => ['ROLE_CLIENT']
            ],
            [
                'client_id' => 11,
                'user_id' => 2,
                'email' => 'user2@example.com',
                'name' => 'Miguel',
                'surnames' => 'García Ruiz',
                'address' => 'Calle Ejemplo, 10',
                'phone_number' => '999999999',
                'roles' => ['ROLE_CLIENT']
            ]];

        $this->assertEquals($expectedData, json_decode($response->getContent(), true));
    }

    /**
     * @throws NotValidEmailException
     * @throws Exception
     */
    private function createUserMock(int $id, string $email, array $roles): User
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getEmail')->willReturn(new EmailValueObject($email));
        $user->method('getRoles')->willReturn($roles);
        return $user;
    }

    /**
     * @throws Exception
     */
    private function createClientMock(int $id, User $user, string $name, string $surnames, string $address, string $phone): Client
    {
        $client = $this->createMock(Client::class);
        $client->method('getId')->willReturn($id);
        $client->method('getUser')->willReturn($user);
        $client->method('getName')->willReturn($name);
        $client->method('getSurnames')->willReturn($surnames);
        $client->method('getAddress')->willReturn($address);
        $client->method('getPhoneNumber')->willReturn($phone);
        return $client;
    }
}
