<?php
declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\User\Application\User\AdminDeleteByIdService;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminDeleteByIdServiceTest extends Unit
{
    private UserRepositoryInterface $repository;

    private AdminDeleteByIdService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new AdminDeleteByIdService(
            $this->repository
        );
    }

    /**
     * @throws Exception
     */
    public function testAdminDeletedSuccessfully(): void
    {
        $userId = 123;

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($userId);

        $this->repository
            ->expects($this->once())
            ->method('findUserByIdOrFail')
            ->with($userId)
            ->willReturn($user);

        $this->repository
            ->expects($this->once())
            ->method('remove')
            ->with($user);

        $response = ($this->service)($user);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Admin deleted successfully', $data['message']);
    }

}
