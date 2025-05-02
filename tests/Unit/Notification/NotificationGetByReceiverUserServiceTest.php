<?php

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationGetByReceiverUserService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationGetByReceiverUserServiceTest extends Unit
{
    private NotificationRepositoryInterface&MockObject $notificationRepository;
    private NotificationGetByReceiverUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->service = new NotificationGetByReceiverUserService($this->notificationRepository);
    }

    /**
     * @throws Exception
     */
    public function testNotificationsRetrievedSuccessfully(): void
    {
        $receiver = $this->createMock(User::class);
        $receiver->method('getEmail')->willReturn(new EmailValueObject('receiver@example.com'));

        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject('creator@example.com'));

        $notification = $this->createMock(Notification::class);
        $notification->method('getId')->willReturn(1);
        $notification->method('getCreatorUser')->willReturn($creator);
        $notification->method('getMessage')->willReturn('Test notification');
        $notification->method('getDate')->willReturn(new \DateTime('2025-04-23 14:00:00'));
        $notification->method('getUser')->willReturn(new ArrayCollection([$receiver]));

        $this->notificationRepository
            ->expects($this->once())
            ->method('findNotificationsByUser')
            ->with($receiver)
            ->willReturn([$notification]);

        $response = ($this->service)($receiver);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);
        $this->assertEquals('Notifications retrieved successfully', $json['message']);
        $this->assertIsArray($json['tasks']);
        $this->assertEquals('Test notification', $json['tasks'][0]['message']);
        $this->assertEquals('creator@example.com', $json['tasks'][0]['creator']);
        $this->assertContains('receiver@example.com', $json['tasks'][0]['receptors_emails']);
    }
}
