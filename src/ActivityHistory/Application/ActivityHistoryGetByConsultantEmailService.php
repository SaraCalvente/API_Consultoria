<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryGetByConsultantEmailService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository,
        private ConsultantRepositoryInterface $consultantRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @return array{current_consultant_id: int, activities: ActivityHistoryDTO[]}
     */
    public function __invoke(array $data): array
    {
        $user = $this->userRepository->findUserByEmailOrFail($data['email']);

        if (!$this->consultantRepository->checkIfConsultantExists($user)) {
            throw new DomainException('The user ' . $user->getEmail() . ' is not a consultant');
        }

        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if (!$consultant) {
            throw new DomainException('Consultant not found for user: ' . $user->getEmail());
        }

        $activities = $this->activityHistoryRepository->findActivitiesByConsultant($user);

        return [
            'current_consultant_id' => $consultant->getId(),
            'activities' => array_map(fn($activity) => ActivityHistoryDTO::fromEntity($activity), $activities),
        ];
    }

}