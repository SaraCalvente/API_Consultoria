<?php

namespace App\Project\Infraestructure;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant\Consultant;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;


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

    public function __construct(
        private EntityManagerInterface $entityManager,
        ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
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
        return $this->entityManager->getRepository(Project::class)->findAll();
    }

    public function checkIfProjectExists(string $name): bool
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $name]);
        return $project !== null;
    }

    public function findProjectByClient(Client $client): array
    {
        return $this->entityManager->getRepository(Project::class)->findBy(['client' => $client]);
    }

    public function checkDates(string $startDate, ?string $endDate): bool
    {
        $start = \DateTime::createFromFormat('Y-m-d', $startDate);
        if (!$start) {
            return false;
        }
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

    public function checkIfClientHasProjects(Client $client): ?JsonResponse
    {
        $projects = $this->findProjectByClient($client);

        if (count($projects) > 0) {
            $projectDetails = array_map(fn($p) => ['id' => $p->getId(), 'name' => $p->getName()], $projects);

            return new JsonResponse([
                'error' => 'Cannot delete client because there are associated projects.',
                'projects' => $projectDetails
            ], 402);
        }
        return null;
    }

    public function checkIfConsultantHasProjects(Consultant $consultant): ?JsonResponse
    {
        $projects = $consultant->getProject()->toArray();

        if (count($projects) > 0) {
            $projectDetails = array_map(fn($p) => ['id' => $p->getId(), 'name' => $p->getName()], $projects);

            return new JsonResponse([
                'error' => 'Cannot delete consultant because there are associated projects.',
                'projects' => $projectDetails
            ], 402);
        }
        return null;
    }
}