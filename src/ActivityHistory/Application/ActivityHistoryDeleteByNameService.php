<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\ActivityHistory\Infraestructure\ActivityHistoryRepository;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ActivityHistoryDeleteByNameService
{
    public function __construct(
        private ActivityHistoryRepositoryInterface $activityHistoryRepository,
        private ProjectRepositoryInterface $projectRepository
    )
    {}

    public function __invoke(array $data): JsonResponse
    {
        if (!$this->projectRepository->checkIfProjectExists($data['projectName'])){
            return new JsonResponse(
                ['error' => 'Project with name ' . $data['projectName'] . ' was not found'],
                404
            );
        }
        $project = $this->projectRepository->findProjectByName($data['projectName']);

        if (!$this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($data['name'], $project)){
            return new JsonResponse(
                ['error' => 'An activity with this name (' . $data['name'] . ') in project ' . $data['projectName'] .
                    ' was not found'],
                404
            );
        }

        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($data['name'], $project);
        $this->activityHistoryRepository->removeActivityHistory($activity);
        return new JsonResponse(
            ['message' => 'Activity deleted successfully'],
            201
        );
    }
}