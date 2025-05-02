<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetByConsultantEmailService
{
    public function __construct(private AvailabilityRepositoryInterface $availabilityRepository, private ConsultantRepositoryInterface $consultantRepository, private UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmailOrFail($data['email']);
        if (!$this->consultantRepository->checkIfConsultantExists($user)) {
            return new JsonResponse([
                'error' => "{$data['email']} is not a Consultant"
            ], 404);
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