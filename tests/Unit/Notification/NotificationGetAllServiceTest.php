<?php

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationGetAllService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\NotificationDTO;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationGetAllServiceTest extends Unit
{
    private NotificationRepositoryInterface&MockObject $notificationRepository;
    private NotificationGetAllService $service;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);

        $this->service = new NotificationGetAllService($this->notificationRepository);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testNotificationsRetrievedSuccessfully(): void
    {
        $notification1 = $this->createMock(Notification::class);
        $notification2 = $this->createMock(Notification::class);

        $user1 = $this->createMock(User::class);
        $user1->method('getEmail')->willReturn(new EmailValueObject('user1@example.com'));

        $user2 = $this->createMock(User::class);
        $user2->method('getEmail')->willReturn(new EmailValueObject('user2@example.com'));

        $notification1->method('getId')->willReturn(1);
        $notification1->method('getCreatorUser')->willReturn($user1);
        $notification1->method('getMessage')->willReturn('Test Message 1');
        $notification1->method('getDate')->willReturn(new \DateTime());
        $notification1->method('getUser')->willReturn(new ArrayCollection([$user1, $user2]));

        $notification2->method('getId')->willReturn(2);
        $notification2->method('getCreatorUser')->willReturn($user2);
        $notification2->method('getMessage')->willReturn('Test Message 2');
        $notification2->method('getDate')->willReturn(new \DateTime());
        $notification2->method('getUser')->willReturn(new ArrayCollection([$user1]));

        $this->notificationRepository
            ->expects($this->once())
            ->method('findAllNotifications')
            ->willReturn([$notification1, $notification2]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Notifications retrieved successfully', $data['message']);
        $this->assertIsArray($data['notifications']);
        $this->assertCount(2, $data['notifications']);

        $this->assertEquals(1, $data['notifications'][0]['notification_id']);
        $this->assertEquals('user1@example.com', $data['notifications'][0]['creator']);
        $this->assertEquals('Test Message 1', $data['notifications'][0]['message']);
        $this->assertArrayHasKey('receptors_emails', $data['notifications'][0]);
        $this->assertContains('user1@example.com', $data['notifications'][0]['receptors_emails']);
        $this->assertContains('user2@example.com', $data['notifications'][0]['receptors_emails']);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testNoNotificationsFound(): void
    {
        $this->notificationRepository
            ->expects($this->once())
            ->method('findAllNotifications')
            ->willReturn([]);

        $response = ($this->service)();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('There are no notifications', $data['error']);
    }
}
