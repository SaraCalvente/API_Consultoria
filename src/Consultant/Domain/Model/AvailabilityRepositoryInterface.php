<?php

namespace App\Consultant\Domain\Model;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Consultant\Consultant;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

interface AvailabilityRepositoryInterface
{
    public function findAllAvailabilities(): array;

    public function findAvailabilityByConsultant(Consultant $consultant): array;

    public function checkIfAvailabilityExists(Consultant $consultant, string $startDate): bool;

    public function findAvailabilityByStartDateAndConsultant(Consultant $consultant, string $startDate): ?Availability;

    public function updateAvailability(Availability $availability, ?string $endDate, ?bool $available): JsonResponse;

    public function deleteAvailability(Availability $availability): JsonResponse;

    public function addAvailability(Availability $availability): void;

    public function saveAvailability(): void;

    public function removeAvailability(Availability $availability): void;
    public function checkDates(string $startDate, string $endDate): bool;

}