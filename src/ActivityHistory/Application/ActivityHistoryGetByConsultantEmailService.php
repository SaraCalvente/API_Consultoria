<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryGetByConsultantEmailService
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        ActivityHistoryRepositoryInterface $activityHistoryRepository,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->activityHistoryRepository = $activityHistoryRepository;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($data['email']);
        if (!$this->consultantRepository->checkIfConsultantExists($user)) {
            return new JsonResponse(['error' => 'The user ' . $user->getEmail() . ' is not a consultant'], 400);
        }
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if (!$consultant) {
            return new JsonResponse(['error' => 'Consultant has no associated activities'], 402);
        }

        $activities = $this->activityHistoryRepository->findActivitiesByConsultant($user);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'current_consultant_id' => $consultant->getId(),
            'activities' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ], 200);
    }

}