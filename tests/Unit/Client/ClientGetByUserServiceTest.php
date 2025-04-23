<?php

declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\Client\Application\ClientGetByUserService;
use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientGetByUserServiceTest extends Unit
{
    /**
     * @throws Exception
     * @throws NotValidEmailException
     */
    public function testInvokeReturnsClientDataAsJsonResponse(): void
    {


        $user = $this->createUserMock(1, 'user@example.com', ['ROLE_CLIENT']);

        $client = $this->createClientMock(1, $user, 'Ana', 'García Ruiz', 'Calle Ejemplo, 9', '666666666');

        $clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $clientRepository
            ->expects($this->once())
            ->method('getClientByUser')
            ->with($user)
            ->willReturn($client);

        $service = new ClientGetByUserService($clientRepository);

        $expectedJson = new JsonResponse(ClientDTO::fromEntity($client));
        $actualJson = $service($user);

        $this->assertEquals($expectedJson->getContent(), $actualJson->getContent());
        $this->assertEquals($expectedJson->getStatusCode(), $actualJson->getStatusCode());
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
