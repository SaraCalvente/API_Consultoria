<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class AvailabilityGetByConsultantAndStartDateService
{
    public function __construct(
        private AvailabilityRepositoryInterface $availabilityRepository,
        private ConsultantRepositoryInterface $consultantRepository,
        private UserRepositoryInterface $userRepository
    )
    {}

    public function __invoke(User $user, array $data): JsonResponse
    {
        if ($data['email']){
            $user = $this->userRepository->findUserByEmailOrFail($data['email']);
            $consultant = $this->consultantRepository->findConsultantByUser($user);
        }
        else{
            $consultant= $this->consultantRepository->findConsultantByUser($user);
        }
        if (!$this->availabilityRepository->checkIfAvailabilityExists($consultant, $data['startDate'])) {
            return new JsonResponse(['error' => 'An availability for ' . $data['email'] . ' with start date ' . $data['startDate'] . ' does not exists.' ], 403);
        }
        $availability = $this->availabilityRepository->findAvailabilityByStartDateAndConsultant($consultant, $data['startDate']);

        return new JsonResponse([
            'message' => 'Availability successfully updated.',
            'availability' => AvailabilityDTO::fromEntity($availability)
        ], 201);
    }
}