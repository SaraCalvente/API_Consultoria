<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\ActivityHistory\Domain\ActivityHistory;
use App\ActivityHistory\Domain\ActivityHistoryDTO;
use App\ActivityHistory\Domain\Model\ActivityHistoryRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryCreateService
{
    private ActivityHistoryRepositoryInterface $activityHistoryRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        ActivityHistoryRepositoryInterface $activityHistoryRepository,
        ProjectRepositoryInterface $projectRepository,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->activityHistoryRepository = $activityHistoryRepository;
        $this->projectRepository = $projectRepository;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(
        array $data
    ): JsonResponse
    {

        if(!$this->projectRepository->checkIfProjectExists($data['projectName'])){
            return new JsonResponse(['error' => 'Project with name ' . $data['projectName'] . ' was not found'], 404);
        }
        $project = $this->projectRepository->findProjectByName($data['projectName']);

        if($this->activityHistoryRepository->checkIfActivityHistoryFromProjectExists($data['name'], $project)){
            return new JsonResponse(['error' => 'A task with this name (' . $data['name'] . ') in project ' . $data['projectName'] . ' already exists'], 403);
        }
        $user = $this->userRepository->findUserByEmail($data['consultantEmail']);
        if (!$this->consultantRepository->checkIfConsultantExists($user)){
            return new JsonResponse(['error' => 'Consultant with name ' . $data['consultantEmail'] . ' was not found'], 404);
        }

        $activityHistory = new ActivityHistory();
        $activityHistory->setName($data['name']);
        $activityHistory->setProject($project);
        $activityHistory->setUser($user);
        $activityHistory->setDescription($data['description']);
        $activityHistory->setDate(new \DateTime($data['date']));
        $project->addActivityHistory($activityHistory);
        $user->addActivityHistory($activityHistory);

        $this->activityHistoryRepository->addActivityHistory($activityHistory);

        return new JsonResponse([
            'message' => 'ActivityHistory created successfully',
            'activity' => ActivityHistoryDTO::fromEntity($activityHistory),
        ], 201);
    }
}