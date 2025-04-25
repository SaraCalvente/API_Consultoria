<?php
declare(strict_types=1);

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationAdminGetByCreatorUserService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationAdminGetByCreatorUserServiceTest extends Unit
{
    private NotificationRepositoryInterface $notificationRepository;
    private UserRepositoryInterface $userRepository;
    private NotificationAdminGetByCreatorUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new NotificationAdminGetByCreatorUserService(
            $this->notificationRepository,
            $this->userRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testNotificationsRetrievedSuccessfully(): void
    {
        $email = 'admin@example.com';

        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject($email));

        $receiver = $this->createMock(User::class);
        $receiver->method('getEmail')->willReturn(new EmailValueObject('receiver@example.com'));

        $notification = $this->createMock(Notification::class);
        $notification->method('getId')->willReturn(1);
        $notification->method('getCreatorUser')->willReturn($creator);
        $notification->method('getMessage')->willReturn('Notificación desde Admin');
        $notification->method('getDate')->willReturn(new \DateTime('2025-04-23 10:00:00'));
        $notification->method('getUser')->willReturn(new ArrayCollection([$receiver]));

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmail')
            ->with($email)
            ->willReturn($creator);

        $this->notificationRepository
            ->expects($this->once())
            ->method('findNotificationsByCreatorUser')
            ->with($creator)
            ->willReturn([$notification]);

        $response = ($this->service)(['email' => $email]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Notifications retrieved successfully', $data['message']);
        $this->assertEquals('Notificación desde Admin', $data['tasks'][0]['message']);
        $this->assertEquals('admin@example.com', $data['tasks'][0]['creator']);
        $this->assertContains('receiver@example.com', $data['tasks'][0]['receptors_emails']);
    }
}
