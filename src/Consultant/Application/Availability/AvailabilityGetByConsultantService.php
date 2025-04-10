<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetByConsultantService
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        AvailabilityRepositoryInterface $availabilityRepository,
        ConsultantRepositoryInterface $consultantRepository,
    )
    {
        $this->availabilityRepository = $availabilityRepository;
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        if(!$this->consultantRepository->checkIfConsultantExists($user)) {
            return new JsonResponse(['error' => 'User ' . $user->getEmail() . ' is not a Consultant'], 404);
        }
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $availabilities = $this->availabilityRepository->findAvailabilityByConsultant($consultant);

        return new JsonResponse([
            'message' => 'Ability successfully retrieved.',
            'consultant_email' => $user->getEmail(),
            'consultant_name' => $consultant->getName(),
            'consultant_id' => $consultant->getId(),
            'availabilities' => array_map(fn($c) => AvailabilityDTO::fromEntity($c), $availabilities),
        ], 201);
    }
}