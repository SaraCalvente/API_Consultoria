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

class AvailabilityUpdateService
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

    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($data['email']);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        if (!$this->availabilityRepository->checkIfAvailabilityExists($consultant, $data['startDate'])) {
            return new JsonResponse(['error' => 'An availability for ' . $data['email'] . ' with start date ' . $data['startDate'] . ' does not exists.'], 403);
        }
        $availability = $this->availabilityRepository->findAvailabilityByStartDateAndConsultant($consultant, $data['startDate']);
        return $this->availabilityRepository->updateAvailability($availability, $data['endDate'], $data['available']);
    }
}