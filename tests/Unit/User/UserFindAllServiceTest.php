<?php
declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\User\Application\UserFindAllService;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserFindAllServiceTest extends Unit
{
    private UserRepositoryInterface $repository;

    private UserFindAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new UserFindAllService($this->repository);
    }

    public function testAllUsersRetrievedSuccessfully(): void
    {
        $users = [
            $this->createMockUser(1, 'user1@example.com', ['ROLE_USER']),
            $this->createMockUser(2, 'user2@example.com', ['ROLE_ADMIN']),
        ];

        $this->repository
            ->expects($this->once())
            ->method('findAllUsers')
            ->willReturn($users);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertCount(2, $data);
        $this->assertEquals(1, $data[0]['user_id']);
        $this->assertEquals('user1@example.com', $data[0]['email']);
        $this->assertEquals(['ROLE_USER'], $data[0]['roles']);

        $this->assertEquals(2, $data[1]['user_id']);
        $this->assertEquals('user2@example.com', $data[1]['email']);
        $this->assertEquals(['ROLE_ADMIN'], $data[1]['roles']);
    }

    private function createMockUser(int $id, string $email, array $roles): User
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getEmail')->willReturn(new EmailValueObject($email));
        $user->method('getRoles')->willReturn($roles);

        return $user;
    }
}
