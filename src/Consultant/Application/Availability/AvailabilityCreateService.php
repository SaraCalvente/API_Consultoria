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
    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($data['email']);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        if ($this->availabilityRepository->checkIfAvailabilityExists($consultant, $data['startDate'])) {
            return new JsonResponse(['error' => 'An availability of ' . $data['email'] .
                ' with a start date ' . $data['startDate'] . ' already exists.' ], 403);
        }
        $availability = new Availability();
        $availability->setAvailable($data['available']);
        $availability->setConsultant($consultant);
        if(!$this->availabilityRepository-> checkDates($data['startDate'], $data['endDate'])){
            return new JsonResponse([
                'error' => 'Start date (' . $data['startDate'] . ') is grater than end date (' . $data['endDate'] . ') or have the wrong format (Y-m-d H:i:s)'
            ], 403);
        }
        $availability->setStartDate(new \DateTime ($data['startDate']));
        $availability->setEndDate(new \DateTime($data['endDate']));
        $this->availabilityRepository->addAvailability($availability);

        return new JsonResponse([
            'message' => 'Consultant successfully registered',
            'ability' => AvailabilityDTO::fromEntity($availability)
        ], 201);
    }
}