<?php
declare(strict_types=1);

namespace App\Tests\Unit\Client;

use App\Client\Application\ClientCreateService;
use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\Shared\Domain\Exception\RequiredFieldException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientCreateServiceTest extends Unit
{
    private ClientCreateService $service;
    private $hasher;
    private $clientRepo;
    private $userRepo;
    private array $data;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->clientRepo = $this->createMock(ClientRepositoryInterface::class);
        $this->userRepo = $this->createMock(UserRepositoryInterface::class);

        $this->service = new ClientCreateService($this->hasher, $this->clientRepo, $this->userRepo);
        $this->data = [
            'email' => 'test@example.com',
            'password' => 'Secure1234',
            'name' => 'Sara',
            'surnames' => 'Calvente',
            'address' => 'Calle ejemplo, 1',
            'phoneNumber' => '600123123',
        ];
    }

    /**
     * @throws RequiredFieldException
     * @throws NotValidEmailException
     */
    public function testClientRegistersSuccessfully(): void
    {
        $this->userRepo->expects($this->once())->method('checkIfUserExists')
            ->with($this->data['email']);

        $this->userRepo->expects($this->once())->method('checkPasswordLength')
            ->with($this->data['password']);

        $this->hasher->expects($this->once())->method('hashPassword')
            ->willReturn('hashed_password');

        $this->userRepo->expects($this->once())->method('add')
            ->with($this->isInstanceOf(User::class));

        $this->clientRepo->expects($this->once())->method('addClient')
            ->with($this->isInstanceOf(Client::class));

        $response = ($this->service)($this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertStringContainsString('Client successfully registered', $response->getContent());
    }

    /**
     * @throws NotValidEmailException
     */
    public function testMissingRequiredFieldThrowsException(): void
    {
        $data = [
            'email' => 'test@example.com',
            'name' => 'Sara',
            'surnames' => 'Calvente',
            'address' => 'Calle ejemplo, 1',
            'phoneNumber' => '600123123',
        ];

        $this->expectException(RequiredFieldException::class);
        ($this->service)($data);
    }
}