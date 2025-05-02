<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityDeleteService
{
    public function __construct(private AvailabilityRepositoryInterface $availabilityRepository, private UserRepositoryInterface $userRepository, private ConsultantRepositoryInterface $consultantRepository)
    {
    }

    public function __invoke(User $user, array $data): JsonResponse
    {
        if ($data['consultantEmail']){
            $user = $this->userRepository->findUserByEmailOrFail($data['consultantEmail']);
            $consultant = $this->consultantRepository->findConsultantByUser($user);
        }
        else{
            $consultant= $this->consultantRepository->findConsultantByUser($user);
        }
        if (!$this->availabilityRepository->checkIfAvailabilityExists($consultant, $data['startDate'])) {
            return new JsonResponse(['error' => 'An availability for ' . $data['consultantEmail'] . ' with start date ' . $data['startDate'] . ' does not exists.'], 403);
        }
        $availability = $this->availabilityRepository->findAvailabilityByStartDateAndConsultant($consultant, $data['startDate']);
        $this->availabilityRepository->deleteAvailability($availability);


        return new JsonResponse([
            'message' => 'Availability successfully deleted.',
        ], 201);
    }
}