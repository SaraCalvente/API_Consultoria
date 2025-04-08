<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\ActivityHistory\Infraestructure\ActivityHistoryRepository;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\TaskDTO;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\Shared\Domain\Exception\TaskNotFoundException;
use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryFindByNameAndProjectService
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ActivityHistoryRepositoryInterface $activityHistoryRepository,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->activityHistoryRepository = $activityHistoryRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(string $name, string $projectName): JsonResponse
    {
        $project = $this->projectRepository->findProjectByName($projectName);
        if (!$project) {
            throw new ProjectNotFoundException();
        }

        if (!$this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($name, $project)) {
            throw new ActivityHistoryNotFoundException();
        }

        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($name, $project);
        return new JsonResponse([
            'message' => 'Tasks retrieved successfully',
            'task' => ActivityHistoryDTO::fromEntity($activity)
        ], 201);
    }

}