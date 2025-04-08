<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\ActivityHistory\Infraestructure\ActivityHistoryRepository;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Status;
use App\Project\Domain\Task;
use App\Project\Domain\TaskDTO;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryUpdateByNameAndProjectService
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;

    public function __construct(
        ActivityHistoryRepositoryInterface $activityHistoryRepository,
        ProjectRepositoryInterface $projectRepository,
    )
    {
        $this->activityHistoryRepository = $activityHistoryRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(
        string $name, string $projectName, string $description = null
    ): JsonResponse {

        $project = $this->projectRepository->findProjectByName($projectName);
        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($name, $project);

        if ($description !== null) {
            $activity->setDescription($description);
        }
        $this->activityHistoryRepository->saveActivityHistory();

        return new JsonResponse([
            'message' => 'Activity updated successfully',
            'project' => ActivityHistoryDTO::fromEntity($activity),
        ], 201);

    }
}