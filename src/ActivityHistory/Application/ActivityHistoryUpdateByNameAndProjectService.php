<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ActivityHistoryNotFromUserException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryUpdateByNameAndProjectService
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ConsultantRepositoryInterface $consultantRepository;

    public function __construct(
        ActivityHistoryRepositoryInterface $activityHistoryRepository,
        ProjectRepositoryInterface $projectRepository,
    ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->activityHistoryRepository = $activityHistoryRepository;
        $this->projectRepository = $projectRepository;
        $this->consultantRepository = $consultantRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke( User $user,
        array $data
    ): JsonResponse {

        $project = $this->projectRepository->findProjectByName($data['projectName']);
        if (!$project) {
            throw new ProjectNotFoundException();
        }

        if (!$this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($data['name'], $project)) {
            throw new ActivityHistoryNotFoundException();
        }

        $activity = $this->activityHistoryRepository->findActivityHistoryFromProject($data['name'], $project);

        $consultantCheck = $this->consultantRepository->checkIfConsultantExists($user);

        if($consultantCheck){
            $consultant = $this->consultantRepository->findConsultantByUser($user);
            if (!$consultant->getUser()->getActivityHistories()->contains($activity)) {
                throw new ActivityHistoryNotFromUserException($data['name'], $data['projectName']);
            }
        }

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