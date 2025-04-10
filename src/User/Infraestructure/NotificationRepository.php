<?php

namespace App\User\Infraestructure;

use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository {
    private EntityManagerInterface $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
        $this->entityManager = $entityManager;
    }

    public function findAllNotifications(): array
    {
        // TODO: Implement findAllNotifications() method.
    }

    /*public function findNotificationsByCreatorUser(User $user): array
    {
        // TODO: Implement findNotificationsByCreatorUser() method.
    }

    public function findNotificationsByUser(User $user): array
    {
        // TODO: Implement findNotificationsByUser() method.
    }

    public function checkIfNotificationExists(string $email, string $date): bool
    {
        // TODO: Implement checkIfNotificationExists() method.
    }

    public function findNotificationByUserAndDate(string $email, string $date): ?Notification
    {
        // TODO: Implement findNotificationByUserAndDate() method.
    }

    public function deleteNotification(Notification $notification): JsonResponse
    {
        // TODO: Implement deleteNotification() method.
    }

    public function addNotification(Notification $notification): void
    {
        // TODO: Implement addNotification() method.
    }

    public function saveNotification(): void
    {
        // TODO: Implement saveNotification() method.
    }

    public function removeNotification(Notification $notification): void
    {
        // TODO: Implement removeNotification() method.
    }*/
}
