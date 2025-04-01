<?php

namespace App\Project\Infraestructure;


use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project;
use App\Project\Domain\Task;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use function PHPUnit\Framework\throwException;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository implements TaskRepositoryInterface
{

    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry        $registry)
    {
        parent::__construct($registry, Task::class);
        $this->entityManager = $entityManager;
    }

    public function add(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function checkIfProjectExists(string $name): ?Project
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);
        if(!$project) {
            throw new ProjectNotFoundException();
        }
        return $project;
    }

    public function checkIfTaskExists(string $name, Project $project): ?Task{
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['name' => $name, 'project' => $project]);
        if(!$task) {
            throw new TaskNotFoundException();
        }
        return $task;
    }

    public function checkDates(string $startDate, ?string $endDate): bool
    {
        $start = \DateTime::createFromFormat('Y-m-d', $startDate);
        if (!$start) return false;
        if ($endDate) {
            $end = \DateTime::createFromFormat('Y-m-d', $endDate);
            return $end && $start <= $end;
        }
        return true;
    }

    public function findUserByEmail(string $email): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }


    public function findConsultantByEmail(string $email): ?Consultant
    {
        $user = $this->findUserByEmail($email);
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }
        return $consultant;
    }

    public function findProjectByName(string $name): ?Project
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);
        if (!$project) {
            throw new ProjectNotFoundException();
        }
        return $project;
    }

}