<?php
declare(strict_types=1);

namespace App\Tests\Unit\User;

use App\User\Application\AdminFindAllService;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PharIo\Manifest\Email;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminFindAllServiceTest extends Unit
{
    private UserRepositoryInterface $repository;

    private AdminFindAllService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new AdminFindAllService($this->repository);
    }

    /**
     * @throws Exception
     */
    public function testAdminsRetrievedSuccessfully(): void
    {
        $admin1 = $this->createMock(User::class);
        $admin1->method('getId')->willReturn(1);
        $admin1->method('getEmail')->willReturn(new EmailValueObject('admin1@example.com'));
        $admin1->method('getRoles')->willReturn(['ROLE_ADMIN']);

        $admin2 = $this->createMock(User::class);
        $admin2->method('getId')->willReturn(2);
        $admin2->method('getEmail')->willReturn(new EmailValueObject('admin2@example.com'));
        $admin2->method('getRoles')->willReturn(['ROLE_ADMIN']);

        $this->repository
            ->expects($this->once())
            ->method('getAllAdmins')
            ->willReturn([$admin1, $admin2]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Admins retrieved successfully', $data['message']);
        $this->assertCount(2, $data['All admins']);
        $this->assertEquals(1, $data['All admins'][0]['user_id']);
        $this->assertEquals('admin1@example.com', $data['All admins'][0]['email']);
        $this->assertEquals(['ROLE_ADMIN'], $data['All admins'][0]['roles']);
        $this->assertEquals(2, $data['All admins'][1]['user_id']);
        $this->assertEquals('admin2@example.com', $data['All admins'][1]['email']);
        $this->assertEquals(['ROLE_ADMIN'], $data['All admins'][1]['roles']);
    }

    public function testNoAdminsFound(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('getAllAdmins')
            ->willReturn([]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Admins retrieved successfully', $data['message']);
        $this->assertCount(0, $data['All admins']);
    }
}
