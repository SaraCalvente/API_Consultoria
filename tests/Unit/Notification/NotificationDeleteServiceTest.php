<?php

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationDeleteService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationDeleteServiceTest extends Unit
{
    private NotificationRepositoryInterface&MockObject $notificationRepository;
    private NotificationDeleteService $service;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);

        $this->service = new NotificationDeleteService($this->notificationRepository);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testNotificationDeletedSuccessfully(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject('user@example.com'));
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $notification = $this->createMock(Notification::class);
        $notification->method('getCreatorUser')->willReturn($user);

        $this->notificationRepository
            ->expects($this->once())
            ->method('findNotificationById')
            ->with(1)
            ->willReturn($notification);

        $this->notificationRepository
            ->expects($this->once())
            ->method('removeNotification')
            ->with($notification);

        $data = ['id' => 1];

        $response = ($this->service)($user, $data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Notification deleted successfully', $data['message']);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testUnauthorizedDeletion(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject('user@example.com'));
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject('creator@example.com'));

        $notification = $this->createMock(Notification::class);
        $notification->method('getCreatorUser')->willReturn($creator);

        $this->notificationRepository
            ->expects($this->once())
            ->method('findNotificationById')
            ->with(1)
            ->willReturn($notification);

        $data = ['id' => 1];

        $response = ($this->service)($user, $data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('User user@example.com is not authorized to delete this notification.', $data['error']);
    }

}
