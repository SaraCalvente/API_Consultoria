<?php
declare(strict_types=1);

namespace App\User\Application\Notification;

use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\NotificationDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationGetAllService
{
    public function __construct(private NotificationRepositoryInterface $notificationRepository)
    {
    }

    public function __invoke(): JsonResponse
    {
        $notifications = $this->notificationRepository->findAllNotifications();

        if ($notifications === []) {
            return new JsonResponse(['error' => 'There are no notifications'], 404);
        }

        return new JsonResponse([
            'message' => 'Notifications retrieved successfully',
            'notifications' => array_map(fn($notification) => NotificationDTO::fromEntity($notification), $notifications),
        ], 200);
    }
}