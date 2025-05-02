<?php

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationGetByReceiverUserAndDateService;
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

class NotificationGetByReceiverUserAndDateServiceTest extends Unit
{
    private NotificationRepositoryInterface&MockObject $notificationRepository;
    private NotificationGetByReceiverUserAndDateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->service = new NotificationGetByReceiverUserAndDateService($this->notificationRepository);
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testNotificationsRetrievedSuccessfully(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject('receiver@example.com'));

        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject('creator@example.com'));

        $notification = $this->createMock(Notification::class);
        $notification->method('getId')->willReturn(1);
        $notification->method('getCreatorUser')->willReturn($creator);
        $notification->method('getMessage')->willReturn('Test message');
        $notification->method('getDate')->willReturn(new \DateTime('2025-04-22 10:00:00'));
        $notification->method('getUser')->willReturn(new ArrayCollection([$user]));

        $this->notificationRepository
            ->expects($this->once())
            ->method('findReceivedNotificationsByUserAndDate')
            ->with($user, new \DateTime('2025-04-22'))
            ->willReturn([$notification]);

        $data = ['date' => '2025-04-22'];

        $response = ($this->service)($user, $data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);
        $this->assertEquals('Notifications retrieved successfully', $json['message']);
        $this->assertIsArray($json['tasks']);
        $this->assertEquals('Test message', $json['tasks'][0]['message']);
        $this->assertEquals('creator@example.com', $json['tasks'][0]['creator']);
        $this->assertContains('receiver@example.com', $json['tasks'][0]['receptors_emails']);
    }
}
