<?php
declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\User\Application\User\UserLoginService;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class UserLoginServiceTest extends Unit
{
    private UserRepositoryInterface $repository;
    private JWTTokenManagerInterface $jwtManager;
    private UserPasswordHasherInterface $passwordHasher;
    private UserLoginService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->service = new UserLoginService(
            $this->passwordHasher,
            $this->jwtManager,
            $this->repository
        );
    }

    /**
     * @throws Exception
     */
    public function testSuccessfulLogin(): void
    {
        $email = 'user@example.com';
        $password = 'password123';

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getEmail')->willReturn(new EmailValueObject($email));

        $this->repository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $password)
            ->willReturn(true);

        $token = 'sample.jwt.token';
        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->with($user)
            ->willReturn($token);

        $response = ($this->service)(['email' => $email, 'password' => $password]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('User logged in successfully', $data['message']);
        $this->assertEquals(1, $data['user_id']);
        $this->assertEquals($token, $data['token']);
    }

    public function testInvalidCredentials(): void
    {
        $email = 'user@example.com';
        $password = 'wrongpassword';

        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject($email));

        $this->repository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $password)
            ->willReturn(false);

        $this->expectException(BadCredentialsException::class);
        $this->expectExceptionMessage('Invalid email or password');

        ($this->service)(['email' => $email, 'password' => $password]);
    }

    public function testTokenGenerationFailure(): void
    {
        $email = 'user@example.com';
        $password = 'password123';

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getEmail')->willReturn(new EmailValueObject($email));

        $this->repository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($user);

        $this->passwordHasher
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($user, $password)
            ->willReturn(true);

        $this->jwtManager
            ->expects($this->once())
            ->method('create')
            ->with($user)
            ->willThrowException(new \Exception('Token generation failed'));

        $response = ($this->service)(['email' => $email, 'password' => $password]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Token generation failed: Token generation failed', $data['error']);
    }
}
