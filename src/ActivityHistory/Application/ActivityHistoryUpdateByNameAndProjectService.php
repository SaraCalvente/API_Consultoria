<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
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
        array $data
    ): JsonResponse {

        $project = $this->projectRepository->findProjectByName($data['projectName']);
        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($data['name'], $project);

        if ($data['description'] !== null) {
            $activity->setDescription($data['description']);
        }
        $this->activityHistoryRepository->saveActivityHistory();

        return new JsonResponse([
            'message' => 'Activity updated successfully',
            'project' => ActivityHistoryDTO::fromEntity($activity),
        ], 201);

    }
}