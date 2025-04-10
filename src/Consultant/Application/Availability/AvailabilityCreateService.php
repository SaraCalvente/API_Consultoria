<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Availability\Availability;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityCreateService
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

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(string $email, string $startDate, string $endDate, bool $available): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        if ($this->availabilityRepository->checkIfAvailabilityExists($consultant, $startDate)) {
            return new JsonResponse(['error' => 'An availability of ' . $email . ' with a start date ' . $startDate . ' already exists.' ], 403);
        }
        $availability = new Availability();
        $availability->setAvailable($available);
        $availability->setConsultant($consultant);
        if(!$this->availabilityRepository-> checkDates($startDate, $endDate)){
            return new JsonResponse([
                'error' => 'Start date (' . $startDate . ') is grater than end date (' . $endDate . ') or have the wrong format (Y-m-d H:i:s)'
            ], 403);
        }
        $availability->setStartDate(new \DateTime ($startDate));
        $availability->setEndDate(new \DateTime($endDate));
        $this->availabilityRepository->addAvailability($availability);

        return new JsonResponse([
            'message' => 'Consultant successfully registered',
            'ability' => AvailabilityDTO::fromEntity($availability)
        ], 201);
    }
}