<?php

namespace App\Project\Infraestructure;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project;
use App\Shared\Domain\Exception\ClientNotFoundException;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository implements ProjectRepositoryInterface
{

    private EntityManagerInterface $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
        $this->entityManager = $entityManager;
    }

    public function addProject(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function findProjectByName(string $name): ?Project
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);
        if (!$project) {
            throw new ProjectNotFoundException();
        }
        return $project;
    }

    public function findAllProjects(): array{
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
        return $projects;
    }

    public function checkIfProjectExists(Project $project): bool
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $project->getName()]);
        if(!$project){
            return false;
        }
        return true;
    }

    public function findProjectByClient(Client $client): array
    {
        return $this->entityManager->getRepository(Project::class)->findBy(['client' => $client]);
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

    public function saveProject(): void{
        $this->entityManager->flush();
    }

    public function removeProject(Project $project): void{
        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }
}