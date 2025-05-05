<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryUpdateDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ActivityHistoryNotFromUserException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryUpdateByNameAndProjectService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository,
        private ProjectRepositoryInterface $projectRepository,
        private ConsultantRepositoryInterface $consultantRepository
    ) {}

    /**
     * @throws ProjectNotFoundException
     * @throws ActivityHistoryNotFoundException
     * @throws ActivityHistoryNotFromUserException
     */
    public function __invoke(User $user, ActivityHistoryUpdateDTO $dto): ActivityHistoryDTO
    {
        $project = $this->projectRepository->findProjectByName($dto->projectName);
        if (!$project) {
            throw new ProjectNotFoundException();
        }

        if (!$this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($dto->name, $project)) {
            throw new ActivityHistoryNotFoundException();
        }

        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($dto->name, $project);

        $consultantCheck = $this->consultantRepository->checkIfConsultantExists($user);
        if ($consultantCheck) {
            $consultant = $this->consultantRepository->findConsultantByUser($user);
            if (!$consultant->getUser()->getActivityHistories()->contains($activity)) {
                throw new ActivityHistoryNotFromUserException($dto->name, $dto->projectName);
            }
        }

        if ($dto->description !== null) {
            $activity->setDescription($dto->description);
        }

        $this->activityHistoryRepository->saveActivityHistory();

        return ActivityHistoryDTO::fromEntity($activity);
    }
}