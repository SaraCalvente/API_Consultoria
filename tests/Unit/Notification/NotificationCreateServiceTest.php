<?php

namespace App\Tests\Unit\User;

use App\User\Application\Notification\NotificationCreateService;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationCreateServiceTest extends Unit
{
    private NotificationRepositoryInterface $notificationRepository;
    private UserRepositoryInterface $userRepository;
    private NotificationCreateService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->service = new NotificationCreateService(
            $this->notificationRepository,
            $this->userRepository
        );
    }

    /**
     * @throws \DateMalformedStringException
     * @throws Exception
     */
    public function testNotificationCreatedSuccessfully(): void
    {
        $creator = $this->createMock(User::class);
        $creator->method('getEmail')->willReturn(new EmailValueObject('creator@example.com'));

        $user1 = $this->createMock(User::class);
        $user1->method('getEmail')->willReturn(new EmailValueObject('user1@example.com'));

        $user2 = $this->createMock(User::class);
        $user2->method('getEmail')->willReturn(new EmailValueObject('user2@example.com'));

        $this->userRepository
            ->expects($this->exactly(2))
            ->method('findUserByEmail')
            ->willReturnMap([
                ['user1@example.com', $user1],
                ['user2@example.com', $user2],
            ]);

        $this->notificationRepository
            ->expects($this->once())
            ->method('addNotification')
            ->with($this->callback(function (Notification $notification) use ($creator, $user1, $user2) {
                $users = $notification->getUser()->toArray();
                return $notification->getCreatorUser() === $creator
                    && $notification->getMessage() === 'This is a test notification'
                    && in_array($user1, $users, true)
                    && in_array($user2, $users, true);
            }));

        $data = [
            'creator' => $creator,
            'message' => 'This is a test notification',
            'usersEmails' => ['user1@example.com', 'user2@example.com'],
        ];

        $response = ($this->service)($creator, $data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals('Notification successfully created', $data['message']);
        $this->assertEquals('creator@example.com', $data['notification']['creator']);
        $this->assertEquals('This is a test notification', $data['notification']['message']);
        $this->assertEquals(['user1@example.com', 'user2@example.com'], $data['notification']['receptors_emails']);
    }
}
