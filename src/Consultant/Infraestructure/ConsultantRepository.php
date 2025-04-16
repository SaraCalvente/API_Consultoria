<?php

namespace App\Consultant\Infraestructure;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use App\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

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


    public function findAllConsultants(): array{
        return $this->entityManager->getRepository(Consultant::class)->findAll();
    }


    public function findConsultantByUser(User $user): ?Consultant
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if (!$consultant) {
            throw new ConsultantNotFoundException($user->getEmail());
        }
        return $consultant;    }

    public function checkIfConsultantExists(User $user): bool
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);
        if (!$consultant) {
            return false;
        }
        return true;
    }

    public function getConsultantByUser(User $user): Consultant
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $user]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $consultant;
    }

   /* public function updateConsultant(User $user, ?string $profile, ?Ability $ability): JsonResponse
    {
        $consultant = $this->findConsultantByUser($user);

        if ($profile !== null) {
            $consultant->setProfile(Profile::from($profile));
        }
        if ($ability !== null) {
            $consultant->addAbility($ability);
        }

        $this->saveConsultant();

        return new JsonResponse(ConsultantDTO::fromEntity($consultant));
    }*/

    public function deleteConsultant(Consultant $consultant): JsonResponse
    {
        $this->removeConsultant($consultant);
        return new JsonResponse(['message' => 'Consultant and associated user deleted successfully'], 200);
    }

    public function addConsultant(Consultant $consultant): void
    {
        $this->entityManager->persist($consultant);
        $this->entityManager->flush();
    }

    public function saveConsultant(): void
    {
        $this->entityManager->flush();
    }

    public function removeConsultant(Consultant $consultant): void
    {
        $this->entityManager->remove($consultant);
        $this->entityManager->flush();
    }
}