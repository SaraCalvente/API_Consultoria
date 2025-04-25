<?php
declare(strict_types=1);

namespace App\User\Application\Notification;

use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\NotificationDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationAdminGetByReceiverUserService
{
    private NotificationRepositoryInterface $notificationRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($data['email']);
        $notifications = $this->notificationRepository->findNotificationsByUser($user);
        return new JsonResponse([
            'message' => 'Notifications retrieved successfully',
            'tasks' => array_map(fn($notification) => NotificationDTO::fromEntity($notification), $notifications),
        ], 200);
    }

}