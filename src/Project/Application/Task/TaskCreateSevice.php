<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Status;
use App\Project\Domain\Task\Task;
use App\Project\Domain\Task\TaskDTO;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskCreateSevice
{
    public function __construct(private TaskRepositoryInterface $taskRepository, private ProjectRepositoryInterface $projectRepository, private ConsultantRepositoryInterface $consultantRepository, private UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke( array $data
    ): JsonResponse
    {

        if (!$this->projectRepository->checkIfProjectExists($data['projectName'])){
            return new JsonResponse(['error' => 'Project with name ' . $data['projectName'] . ' was not found'], 404);
        }

        $project = $this->projectRepository->findProjectByName($data['projectName']);

        if ($this->taskRepository->checkIfTaskFromProjectExists($data['name'], $project)){
            return new JsonResponse(['error' => 'A task with this name (' . $data['name'] . ') in project ' . $data['projectName'] . ' already exists'], 403);
        }

        if (!$this->taskRepository->checkDates($data['startDate'], $data['endDate'])) {
            return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 405);
        }

        $task = new Task();
        $task->setProject($project);
        $task->setName($data['name']);
        $task->setDescription($data['description']);
        $task->setStartDate(new \DateTime($data['startDate']));
        $task->setEndDate(new \DateTime($data['endDate']));
        $task->setStatus(Status::from($data['status']));
        foreach ($data['consultantsEmails'] as $consultantEmail) {
            $consultantUser = $this->userRepository->findUserByEmailOrFail($consultantEmail);
            $consultant = $this->consultantRepository->findConsultantByUser($consultantUser);
            $task->addConsultant($consultant);
        }

        $this->taskRepository->addTask($task);

        return new JsonResponse([
            'message' => 'Task created successfully',
            'task' => TaskDTO::fromEntity($task),
        ], 201);
    }
}