<?php

namespace App\Consultant\Infraestructure;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
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
class AvailabilityRepository extends ServiceEntityRepository implements AvailabilityRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        ManagerRegistry $registry)
    {
        parent::__construct($registry, Availability::class);
    }


    public function findAllAvailabilities(): array{
        return $this->entityManager->getRepository(Availability::class)->findAll();
    }


    public function findAvailabilityByConsultant(Consultant $consultant): array
    {
        if (!$consultant) {
            throw new ConsultantNotFoundException();
        }
        return $this->entityManager->getRepository(Availability::class)->findBy(['consultant' => $consultant]);
    }

    public function checkIfAvailabilityExists(Consultant $consultant, string $startDate): bool
    {
        $availability = $this->findAvailabilityByStartDateAndConsultant($consultant, $startDate);
        return $availability instanceof \App\Consultant\Domain\Availability\Availability;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function findAvailabilityByStartDateAndConsultant(Consultant $consultant, string $startDate): ?Availability
    {
        $startDate = new \DateTime($startDate);
        return $this->entityManager->getRepository(Availability::class)->findOneBy(['consultant' => $consultant, 'start_date' => $startDate]);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function updateAvailability(Availability $availability, ?string $endDate, ?bool $available): JsonResponse
    {
        if ($endDate !== null) {
            $availability->setEndDate(new \DateTime($endDate));
        }
        if ($available !== null) {
            $availability->setAvailable($available);
        }
        $this->saveAvailability();
        return new JsonResponse([
            'message' => 'Availability successfully updated.',
            'ability' => AvailabilityDTO::fromEntity($availability)
        ], 201);
    }

    public function deleteAvailability(Availability $availability): JsonResponse
    {
        $this->removeAvailability($availability);
        return new JsonResponse(['message' => 'Availability deleted successfully'], 200);
    }

    public function addAvailability(Availability $availability): void
    {
        $this->entityManager->persist($availability);
        $this->entityManager->flush();
    }

    public function saveAvailability(): void
    {
        $this->entityManager->flush();
    }

    public function removeAvailability(Availability $availability): void
    {
        $this->entityManager->remove($availability);
        $this->entityManager->flush();
    }

    public function checkDates(string $startDate, string $endDate): bool
    {
        $start = \DateTime::createFromFormat('Y-m-d H:i:s', $startDate);
        if (!$start) {
            return false;
        }
        if ($endDate !== '' && $endDate !== '0') {
            $end = \DateTime::createFromFormat('Y-m-d H:i:s', $endDate);
            return $end && $start <= $end;
        }
        return true;
    }

}
