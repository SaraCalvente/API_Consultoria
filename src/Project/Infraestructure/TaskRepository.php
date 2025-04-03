<?php

namespace App\Project\Infraestructure;


use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
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
    private ConsultantRepositoryInterface $consultantRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry        $registry, ConsultantRepositoryInterface $consultantRepository)
    {
        parent::__construct($registry, Task::class);
        $this->entityManager = $entityManager;
        $this->consultantRepository = $consultantRepository;
    }

    public function addTask(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function checkIfTaskFromProjectExists(string $taskName, Project $project): bool{
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['name' => $taskName, 'project' => $project]);
        if(!$task) {
            return false;
        }
        return true;
    }

    public function findTaskFromProject(string $taskName, Project $project): Task
    {
        return $this->entityManager->getRepository(Task::class)->findOneBy(['name' => $taskName, 'project' => $project]);
    }

    public function findAllTasks(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }


    public function findTaskByConsultant(Consultant $consultant): array
    {
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }
        return $consultant->getTasks()->toArray();
    }
    public function checkDates(string $startDate, ?string $endDate): bool
    {
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate);
        if (!$start) return false;
        if ($endDate) {
            $end = \DateTime::createFromFormat('Y-m-d H:i:s', $endDate);
            return $end && $start <= $end;
        }
        return true;
    }

    public function findTasksByProject(Project $project): array{
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['project' => $project]);
        return $tasks;
    }

    public function saveTask(): void{
        $this->entityManager->flush();
    }

    public function removeTask(Task $task): void{
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}