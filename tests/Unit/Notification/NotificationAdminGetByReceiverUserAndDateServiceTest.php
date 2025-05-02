<?php
declare(strict_types=1);

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationAdminGetByReceiverUserAndDateService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationAdminGetByReceiverUserAndDateServiceTest extends Unit
{
    private NotificationRepositoryInterface $notificationRepository;
    private UserRepositoryInterface $userRepository;
    private NotificationAdminGetByReceiverUserAndDateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new NotificationAdminGetByReceiverUserAndDateService(
            $this->notificationRepository,
            $this->userRepository
        );
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testNotificationsRetrievedSuccessfully(): void
    {
        $email = 'receiver@example.com';
        $date = '2025-04-23 10:00:00';

        $receiver = $this->createMock(User::class);
        $receiver->method('getEmail')->willReturn(new EmailValueObject($email));

        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject('admin@example.com'));

        $notification = $this->createMock(Notification::class);
        $notification->method('getId')->willReturn(1);
        $notification->method('getCreatorUser')->willReturn($creator);
        $notification->method('getMessage')->willReturn('Mensaje para receptor');
        $notification->method('getDate')->willReturn(new \DateTime($date));
        $notification->method('getUser')->willReturn(new ArrayCollection([$receiver]));

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmailOrFail')
            ->with($email)
            ->willReturn($receiver);

        $this->notificationRepository
            ->expects($this->once())
            ->method('findReceivedNotificationsByUserAndDate')
            ->with($receiver, new \DateTime($date))
            ->willReturn([$notification]);

        $response = ($this->service)(['email' => $email, 'date' => $date]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Notifications retrieved successfully', $data['message']);
        $this->assertEquals('Mensaje para receptor', $data['tasks'][0]['message']);
        $this->assertEquals('admin@example.com', $data['tasks'][0]['creator']);
        $this->assertContains('receiver@example.com', $data['tasks'][0]['receptors_emails']);
    }

}
