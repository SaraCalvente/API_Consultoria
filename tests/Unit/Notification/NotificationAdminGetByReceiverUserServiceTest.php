<?php
declare(strict_types=1);

namespace App\Tests\Unit\Notification;

use App\User\Application\Notification\NotificationAdminGetByReceiverUserService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationAdminGetByReceiverUserServiceTest extends Unit
{
    private NotificationRepositoryInterface $notificationRepository;

    private UserRepositoryInterface $userRepository;

    private NotificationAdminGetByReceiverUserService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new NotificationAdminGetByReceiverUserService(
            $this->notificationRepository,
            $this->userRepository
        );
    }

    /**
     * @throws Exception
     */
    public function testNotificationsRetrievedSuccessfully(): void
    {
        $email = 'receiver@example.com';

        $receiver = $this->createMock(User::class);
        $receiver->method('getEmail')->willReturn(new EmailValueObject($email));

        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject('admin@example.com'));

        $notification = $this->createMock(Notification::class);
        $notification->method('getId')->willReturn(1);
        $notification->method('getCreatorUser')->willReturn($creator);
        $notification->method('getMessage')->willReturn('Mensaje recibido');
        $notification->method('getDate')->willReturn(new \DateTime('2025-04-23 10:00:00'));
        $notification->method('getUser')->willReturn(new ArrayCollection([$receiver]));

        $this->userRepository
            ->expects($this->once())
            ->method('findUserByEmailOrFail')
            ->with($email)
            ->willReturn($receiver);

        $this->notificationRepository
            ->expects($this->once())
            ->method('findNotificationsByUser')
            ->with($receiver)
            ->willReturn([$notification]);

        $response = ($this->service)(['email' => $email]);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Notifications retrieved successfully', $data['message']);
        $this->assertEquals('Mensaje recibido', $data['tasks'][0]['message']);
        $this->assertEquals('admin@example.com', $data['tasks'][0]['creator']);
        $this->assertContains('receiver@example.com', $data['tasks'][0]['receptors_emails']);
    }

}
