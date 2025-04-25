<?php
declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\User\Application\User\AdminRegisterService;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminRegisterServiceTest extends Unit
{
    private UserRepositoryInterface $repository;
    private UserPasswordHasherInterface $passwordHasher;
    private AdminRegisterService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->service = new AdminRegisterService($this->passwordHasher, $this->repository);
    }

    /**
     * @throws Exception
     */
    public function testAdminRegisteredSuccessfully(): void
    {
        $data = [
            'email' => 'newadmin@example.com',
            'password' => 'securepassword'
        ];

        $this->repository
            ->expects($this->once())
            ->method('checkIfUserExists')
            ->with($data['email'])
            ->willReturn(false);

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), $data['password'])
            ->willReturn('hashedpassword');

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getEmail')->willReturn(new EmailValueObject($data['email']));
        $user->method('getRoles')->willReturn(['ROLE_ADMIN']);
        $this->repository
            ->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(User::class));

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Admin registered successfully', $data['message']);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('newadmin@example.com', $data['email']);
        $this->assertEquals(['ROLE_ADMIN'], $data['roles']);
    }

    public function testUserAlreadyExists(): void
    {
        $data = [
            'email' => 'existingadmin@example.com',
            'password' => 'securepassword'
        ];

        $this->repository
            ->expects($this->once())
            ->method('checkIfUserExists')
            ->with($data['email'])
            ->willReturn(true);

        $response = ($this->service)($data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(402, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('User existingadmin@example.com already exists', $data['error']);
    }
}
