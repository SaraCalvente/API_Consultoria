<?php

namespace App\Consultant\Infraestructure;

use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Consultant\Domain\Profile;
use App\Project\Domain\Project;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ServiceEntityRepository<Consultant>
 *
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultantRepository extends ServiceEntityRepository implements ConsultantRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultant::class);
        $this->entityManager = $entityManager;
    }

    public function add(Consultant $consultant): void
    {
        $this->entityManager->persist($consultant);
        $this->entityManager->flush();
    }

    public function findUserAndConsultant(array $criteria): array
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy($criteria);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }

        return [$user, $consultant];
    }

    public function checkIfUserExists(string $email): bool{
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            return false;
        }
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if ($consultant) {
            return false;
        }
        return true;
    }

    public function findConsultantById(int $user): ?Consultant{
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }
        return $consultant;
    }

    public function findAllConsultants(): array{
        return $this->entityManager->getRepository(Consultant::class)->findAll();
    }

    public function modifyConsultant(array $criteria, ?string $profile): JsonResponse
    {
        [, $consultant] = $this->findUserAndConsultant($criteria);


        if ($profile !== null) {
            $consultant->setProfile(Profile::from($profile));
        }
        $this->entityManager->flush();

        return new JsonResponse(ConsultantDTO::fromEntity($consultant));
    }

    public function removeConsultant(array $criteria): JsonResponse
    {
        [$user, $consultant] = $this->findUserAndConsultant($criteria);

        $projects = $consultant->getProject();
        if (count($projects) > 0) {
            return new JsonResponse(['error' => 'Cannot delete consultant because there are associated projects.'], 400);
        }

        $this->entityManager->remove($consultant);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Consultant and associated user deleted successfully'], 200);
    }

    public function save(): void{
        $this->entityManager->flush();
    }
}