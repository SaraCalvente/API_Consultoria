<?php

namespace App\Project\Infraestructure;


use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Task\Task;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

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

    public function __construct(
        private EntityManagerInterface $entityManager,
        ManagerRegistry        $registry, )
    {
        parent::__construct($registry, Task::class);
    }

    public function addTask(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function checkIfTaskFromProjectExists(string $taskName, Project $project): bool{
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['name' => $taskName, 'project' => $project]);
        return $task !== null;
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
        if (!$start) {
            return false;
        }
        if ($endDate) {
            $end = \DateTime::createFromFormat('Y-m-d H:i:s', $endDate);
            return $end && $start <= $end;
        }
        return true;
    }

    public function findTasksByProject(Project $project): array{
        return $this->entityManager->getRepository(Task::class)->findBy(['project' => $project]);
    }

    public function saveTask(): void{
        $this->entityManager->flush();
    }

    public function removeTask(Task $task): void{
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}