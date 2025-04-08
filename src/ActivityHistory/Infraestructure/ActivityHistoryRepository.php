<?php

namespace App\ActivityHistory\Infraestructure;

use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Consultant;
use App\Project\Domain\Project;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivityHistory>
 *
 * @method ActivityHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityHistory[]    findAll()
 * @method ActivityHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityHistoryRepository extends ServiceEntityRepository implements ActivityHistoryRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry        $registry, )
    {
        parent::__construct($registry, ActivityHistory::class);
        $this->entityManager = $entityManager;
    }


    public function checkIfActivityHistoryFromProjectExists(string $activityHistoryName, Project $project): bool{
        $activity = $this->entityManager->getRepository(ActivityHistory::class)->findOneBy(['name' => $activityHistoryName, 'project' => $project]);
        if(!$activity) {
            return false;
        }
        return true;
    }

    public function addActivityHistory(ActivityHistory $activityHistory): void
    {
        $this->entityManager->persist($activityHistory);
        $this->entityManager->flush();    }

    public function findActivityHistoryFromProject(string $activityHistoryName, Project $project): ActivityHistory
    {
        return $this->entityManager->getRepository(ActivityHistory::class)->findOneBy(['name' => $activityHistoryName, 'project' => $project]);
    }

    public function findAllActivityHistories(): array
    {
        return $this->entityManager->getRepository(ActivityHistory::class)->findAll();
    }

    public function findActivitiesHistoriesByProject(Project $project): array
    {
        return $this->entityManager->getRepository(ActivityHistory::class)->findBy(['project' => $project]);
    }

    public function saveActivityHistory(): void
    {
        $this->entityManager->flush();
    }

    public function removeActivityHistory(ActivityHistory $activityHistory): void
    {
        $this->entityManager->remove($activityHistory);
        $this->entityManager->flush();
    }

    public function findActivitiesByConsultant(User $user): array
    {
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user->getActivityHistories()->toArray();
    }

}
