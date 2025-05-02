<?php

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationGetByCreatorUserAndDateService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\NotificationDTO;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PharIo\Manifest\Email;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationGetByCreatorUserAndDateServiceTest extends Unit
{
    private NotificationRepositoryInterface&MockObject $notificationRepository;
    private NotificationGetByCreatorUserAndDateService $service;

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);

        $this->service = new NotificationGetByCreatorUserAndDateService($this->notificationRepository);
    }

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \DateMalformedStringException
     */
    public function testNotificationsRetrievedSuccessfullyByCreatorAndDate(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getEmail')->willReturn(new EmailValueObject('creator@example.com'));

        $notification1 = $this->createMock(Notification::class);
        $notification2 = $this->createMock(Notification::class);

        $user1 = $this->createMock(User::class);
        $user1->method('getEmail')->willReturn(new EmailValueObject('user1@example.com'));

        $user2 = $this->createMock(User::class);
        $user2->method('getEmail')->willReturn(new EmailValueObject('user2@example.com'));

        $notification1->method('getId')->willReturn(1);
        $notification1->method('getCreatorUser')->willReturn($user);
        $notification1->method('getMessage')->willReturn('Test Message 1');
        $notification1->method('getDate')->willReturn(new \DateTime('2025-04-22'));
        $notification1->method('getUser')->willReturn(new ArrayCollection([$user1, $user2]));

        $notification2->method('getId')->willReturn(2);
        $notification2->method('getCreatorUser')->willReturn($user);
        $notification2->method('getMessage')->willReturn('Test Message 2');
        $notification2->method('getDate')->willReturn(new \DateTime('2025-04-22'));
        $notification2->method('getUser')->willReturn(new ArrayCollection([$user1]));

        $this->notificationRepository
            ->expects($this->once())
            ->method('findCreatedNotificationsByUserAndDate')
            ->with($user, new \DateTime('2025-04-22'))
            ->willReturn([$notification1, $notification2]);

        $response = ($this->service)($user, ['date' => '2025-04-22']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Notifications retrieved successfully', $data['message']);
        $this->assertIsArray($data['tasks']);
        $this->assertCount(2, $data['tasks']);

        $this->assertEquals(1, $data['tasks'][0]['notification_id']);
        $this->assertEquals('creator@example.com', $data['tasks'][0]['creator']);
        $this->assertEquals('Test Message 1', $data['tasks'][0]['message']);
        $this->assertArrayHasKey('receptors_emails', $data['tasks'][0]);
        $this->assertContains('user1@example.com', $data['tasks'][0]['receptors_emails']);
        $this->assertContains('user2@example.com', $data['tasks'][0]['receptors_emails']);
    }

}
