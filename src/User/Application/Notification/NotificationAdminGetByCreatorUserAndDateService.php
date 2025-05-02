<?php
declare(strict_types=1);

namespace App\User\Application\Notification;

use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\NotificationDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class NotificationAdminGetByCreatorUserAndDateService
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private USerRepositoryInterface $userRepository
    )
    {}

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmailOrFail($data['email']);
        $date = new \DateTime($data['date']);
        $notifications = $this->notificationRepository->findCreatedNotificationsByUserAndDate($user, $date);
        return new JsonResponse([
            'message' => 'Notifications retrieved successfully',
            'tasks' => array_map(fn($notification) => NotificationDTO::fromEntity($notification), $notifications),
        ], 200);
    }

}