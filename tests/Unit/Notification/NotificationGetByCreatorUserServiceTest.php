<?php

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationGetByCreatorUserService;
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

class NotificationGetByCreatorUserServiceTest extends Unit
{
    private NotificationRepositoryInterface&MockObject $notificationRepository;
    private NotificationGetByCreatorUserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->service = new NotificationGetByCreatorUserService($this->notificationRepository);
    }

    /**
     * @throws Exception
     */
    public function testNotificationsRetrievedSuccessfully(): void
    {
        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject('creator@example.com'));

        $user1 = $this->createMock(User::class);
        $user1->method('getEmail')->willReturn(new EmailValueObject('receptor1@example.com'));

        $notification = $this->createMock(Notification::class);
        $notification->method('getId')->willReturn(1);
        $notification->method('getCreatorUser')->willReturn($creator);
        $notification->method('getMessage')->willReturn('Test Notification');
        $notification->method('getDate')->willReturn(new \DateTime('2025-04-20'));
        $notification->method('getUser')->willReturn(new ArrayCollection([$user1]));

        $this->notificationRepository
            ->expects($this->once())
            ->method('findNotificationsByCreatorUser')
            ->with($creator)
            ->willReturn([$notification]);

        $response = ($this->service)($creator);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Notifications retrieved successfully', $data['message']);
        $this->assertCount(1, $data['tasks']);
        $this->assertEquals(1, $data['tasks'][0]['notification_id']);
        $this->assertEquals('creator@example.com', $data['tasks'][0]['creator']);
        $this->assertEquals('Test Notification', $data['tasks'][0]['message']);
        $this->assertContains('receptor1@example.com', $data['tasks'][0]['receptors_emails']);
    }

}
