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

    public function checkIfTaskExists(string $name, Project $project): bool{
        $task = $this->entityManager->getRepository(Task::class)->findOneBy(['name' => $name, 'project' => $project]);
        if(!$task) {
            return false;
        }
        return true;
    }

    public function findTaskFromProject(string $name, Project $project): Task
    {
        return $this->entityManager->getRepository(Task::class)->findOneBy(['name' => $name, 'project' => $project]);
    }

    public function findAllTasks(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }


    public function findTaskByConsultant(string $email): array
    {
        $consultant = $this->consultantRepository->findConsultantByEmail($email);
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

    public function findUserByEmail(string $email): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function findConsultantById(int $id): ?Consultant
    {
        return $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $id]);
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

    public function findTasksByProject(string $projectName): array{
        $project = $this->findProjectByName($projectName);
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['project' => $project]);
        return $tasks;
    }

    public function save(): void{
        $this->entityManager->flush();
    }

    public function remove(Task $task): void{
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}