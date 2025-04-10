<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityDeleteService
{
    private AvailabilityRepositoryInterface $availabilityRepository;
    private UserRepositoryInterface $userRepository;
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        AvailabilityRepositoryInterface $availabilityRepository,
        UserRepositoryInterface $userRepository,
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->availabilityRepository = $availabilityRepository;
        $this->userRepository = $userRepository;
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(string $email, string $startDate): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);
        $consultant= $this->consultantRepository->findConsultantByUser($user);
        if (!$this->availabilityRepository->checkIfAvailabilityExists($consultant, $startDate)) {
            return new JsonResponse(['error' => 'An availability for ' . $email . ' with start date ' . $startDate . ' does not exists.'], 403);
        }
        $availability = $this->availabilityRepository->findAvailabilityByStartDateAndConsultant($consultant, $startDate);
        $this->availabilityRepository->deleteAvailability($availability);


        return new JsonResponse([
            'message' => 'Availability successfully deleted.',
        ], 201);
    }
}