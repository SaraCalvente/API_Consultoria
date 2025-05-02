<?php
declare(strict_types=1);

namespace App\User\Application\Notification;

use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationDeleteService
{
    public function __construct(private NotificationRepositoryInterface $notificationRepository)
    {
    }

    public function __invoke(User $user, array $data): JsonResponse
    {
        $notification = $this->notificationRepository->findNotificationById((int)$data['id']);
        if ($notification->getCreatorUser() === $user || in_array("ROLE_ADMIN", $user->getRoles(), true)) {
            $this->notificationRepository->removeNotification($notification);
            return new JsonResponse(['message' => 'Notification deleted successfully'], 200);
        }
        return new JsonResponse(['error' => 'User ' . $user->getEmail() . ' is not authorized to delete this notification.'], 403);
    }
}