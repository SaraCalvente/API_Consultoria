<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\DTO\ActivityHistoryCreateDTO;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use DateTime;
use DomainException;

final readonly class ActivityHistoryCreateService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository,
        private ProjectRepositoryInterface                  $projectRepository,
        private ConsultantRepositoryInterface               $consultantRepository,
        private UserRepositoryInterface                     $userRepository
    )
    {}

    /**
     * @throws \Exception
     */
    public function __invoke(ActivityHistoryCreateDTO $dto): ActivityHistoryDTO
    {
        if (!$this->projectRepository->checkIfProjectExists($dto->projectName)) {
            throw new DomainException("Project with name {$dto->projectName} was not found");
        }
        $project = $this->projectRepository->findProjectByName($dto->projectName);

        if ($this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($dto->name, $project)) {
            throw new DomainException("Activity '{$dto->name}' in project '{$dto->projectName}' already exists");
        }

        $user = $this->userRepository->findUserByEmailOrFail($dto->consultantEmail);

        if (!$this->consultantRepository->checkIfConsultantExists($user)) {
            throw new DomainException("Consultant with email {$dto->consultantEmail} was not found");
        }

        $activityHistory = ActivityHistory::createActivityHistory(
            $dto->name,
            $dto->description,
            new DateTime($dto->date),
            $user,
            $project
        );

        $this->activityHistoryRepository->addActivityHistory($activityHistory);

        return ActivityHistoryDTO::fromEntity($activityHistory);
    }
}