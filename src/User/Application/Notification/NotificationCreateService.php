<?php
declare(strict_types=1);

namespace App\User\Application\Notification;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\NotificationDTO;
use App\User\Domain\User;
use App\User\Infraestructure\NotificationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use function Symfony\Component\Clock\now;

class NotificationCreateService
{
    public function __construct(private NotificationRepositoryInterface $notificationRepository, private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(User $creator, array $data): JsonResponse
    {
        $notification = new Notification();
        $notification->setCreatorUser($creator);
        $notification->setDate(new \DateTime);
        $notification->setMessage($data['message']);

        foreach ($data['usersEmails'] as $email) {
            $user = $this->userRepository->findUserByEmailOrFail($email);
            $notification->addUser($user);
        }
        $this->notificationRepository->addNotification($notification);

        return new JsonResponse([
            'message' => 'Notification successfully created',
            'notification' => NotificationDTO::fromEntity($notification)
        ], 201);
    }
}