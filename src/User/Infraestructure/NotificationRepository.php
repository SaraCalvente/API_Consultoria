<?php

namespace App\User\Infraestructure;

use App\Project\Domain\Task\Task;
use App\User\Domain\Model\NotificationRepositoryInterface;
use App\User\Domain\Notification;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
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
class NotificationRepository extends ServiceEntityRepository implements NotificationRepositoryInterface{
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
        return $this->entityManager->getRepository(Notification::class)->findAll();
    }

    public function findNotificationsByCreatorUser(User $user): array
    {
        return $this->entityManager->getRepository(Notification::class)->findBy(['creatorUser' => $user]);
    }

    public function findNotificationsByUser(User $user): array
    {
        return $user->getNotifications()->toArray();
    }

    public function checkIfNotificationExists(User $user, string $date): bool
    {
        $dateObj = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if (!$dateObj) {
            return false;
        }

        $notification = $this->findNotificationByUserAndDate($user, $dateObj);
        return $notification !== null;
    }

    public function findNotificationByUserAndDate(User $user, \DateTime $date): ?Notification
    {
        return $this->entityManager->getRepository(Notification::class)->findOneBy([
            'creatorUser' => $user,
            'date' => $date
        ]);
    }

    public function findReceivedNotificationsByUserAndDate(User $user, \DateTime $date): array
    {
        $start = (clone $date)->setTime(0, 0, 0);
        $end = (clone $date)->setTime(23, 59, 59);

        $notifications = $this->createQueryBuilder('n')
            ->innerJoin('n.user', 'u')
            ->where('u = :user')
            ->andWhere('n.date BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        if (empty($notifications)) {
            throw new \Exception('No received notifications found for user ' . $user->getEmail() . ' in ' . $date->format('Y-m-d'));
        }
        return $notifications;
    }

    public function findCreatedNotificationsByUserAndDate(User $user, \DateTime $date): array
    {
        $start = (clone $date)->setTime(0, 0, 0);
        $end = (clone $date)->setTime(23, 59, 59);

        $notifications = $this->createQueryBuilder('n')
            ->where('n.creatorUser = :user')
            ->andWhere('n.date BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
        if (empty($notifications)) {
            throw new \Exception('No created notifications found for user ' . $user->getEmail() . ' in ' . $date->format('Y-m-d'));
        }
        return $notifications;
    }


    public function addNotification(Notification $notification): void
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function removeNotification(Notification $notification): void
    {
        $this->entityManager->remove($notification);
        $this->entityManager->flush();
    }

    public function findNotificationById(int $id): Notification
    {
        return $this->entityManager->getRepository(Notification::class)->findOneBy(['id' => $id]);
    }
}
