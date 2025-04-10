<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetByConsultantAndStartDateService
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        AvailabilityRepositoryInterface $availabilityRepository,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->availabilityRepository = $availabilityRepository;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $email, string $startDate): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        if (!$this->availabilityRepository->checkIfAvailabilityExists($consultant, $startDate)) {
            return new JsonResponse(['error' => 'An availability for ' . $email . ' with start date ' . $startDate . ' does not exists.' ], 403);
        }
        $availability = $this->availabilityRepository->findAvailabilityByStartDateAndConsultant($consultant, $startDate);

        return new JsonResponse([
            'message' => 'Availability successfully updated.',
            'availability' => AvailabilityDTO::fromEntity($availability)
        ], 201);
    }
}