<?php
declare(strict_types=1);

namespace App\User\Application\Notification;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\NotificationDTO;
use App\User\Domain\User;
use App\User\Infraestructure\NotificationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationFindByReceiverUserAndDateService
{
    private NotificationRepositoryInterface $notificationRepository;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository
    )
    {
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(User $user, string $date): JsonResponse
    {
        $date = new \DateTime($date);
        $notifications = $this->notificationRepository->findReceivedNotificationsByUserAndDate($user, $date);
        return new JsonResponse([
            'message' => 'Notifications retrieved successfully',
            'tasks' => array_map(fn($notification) => NotificationDTO::fromEntity($notification), $notifications),
        ], 200);
    }

}