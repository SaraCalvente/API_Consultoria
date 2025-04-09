<?php

namespace App\Consultant\Infraestructure;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Availability;
use App\Consultant\Domain\Consultant\Consultant;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<Availability>
 *
 * @method Availability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Availability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Availability[]    findAll()
 * @method Availability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvailabilityRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry $registry)
    {
        parent::__construct($registry, Ability::class);
        $this->entityManager = $entityManager;
    }


    public function findAllAbilities(): array{
        return $this->entityManager->getRepository(Ability::class)->findAll();
    }


    public function findAbilitiesByConsultant(Consultant $consultant): array
    {
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }
        return $consultant->getAbilities()->toArray();
    }

    public function checkIfAbilityExists(string $name, string $level): bool
    {
        $ability = $this->entityManager->getRepository(Ability::class)->findAbilityByNameAndLevel($name, $level);
        if (!$ability) {
            return false;
        }
        return true;
    }

    public function findAbilityByNameAndLevel(string $name, string $level): ?Ability{
        return $this->entityManager->getRepository(Ability::class)->findOneBy(['name' => $name, 'level' => $level]);
    }

    public function updateAbility(Ability $ability, ?string $name, ?string $level): JsonResponse
    {
        if ($level !== null) {
            $ability->setLevel(Level::from($level));
        }
        if ($name !== null) {
            $ability->setName($name);
        }
        $this->saveAbility();

        return new JsonResponse(AbilityDTO::fromEntity($ability));
    }

    public function deleteAbility(Ability $ability): JsonResponse
    {
        $consultants = $ability->getConsultant()->toArray();
        foreach ($consultants as $consultant) {
            $consultant->removeAbility($ability);
        }
        $this->removeAbility($ability);
        return new JsonResponse(['message' => 'Consultant and associated user deleted successfully'], 200);
    }

    public function addAbility(Ability $ability): void
    {
        $this->entityManager->persist($ability);
        $this->entityManager->flush();
    }

    public function saveAbility(): void
    {
        $this->entityManager->flush();
    }

    public function removeAbility(Ability $ability): void
    {
        $this->entityManager->remove($ability);
        $this->entityManager->flush();
    }
}
