<?php
declare(strict_types=1);

namespace App\User\Application\Notification\Admin;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Task\TaskDTO;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\NotificationDTO;
use App\User\Domain\User;
use App\User\Infraestructure\NotificationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationAdminFindByCreatorUserAndDateService
{
    private NotificationRepositoryInterface $notificationRepository;
    private USerRepositoryInterface $userRepository;

    public function __construct(
        NotificationRepositoryInterface $notificationRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($data['email']);
        $date = new \DateTime($data['date']);
        $notifications = $this->notificationRepository->findCreatedNotificationsByUserAndDate($user, $date);
        return new JsonResponse([
            'message' => 'Notifications retrieved successfully',
            'tasks' => array_map(fn($notification) => NotificationDTO::fromEntity($notification), $notifications),
        ], 200);
    }

}