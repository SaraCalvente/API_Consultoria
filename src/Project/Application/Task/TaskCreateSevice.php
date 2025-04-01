<?php

namespace App\Project\Application\Task;

use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\Status;
use App\Project\Domain\Task;
use App\Project\Domain\TaskDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskCreateSevice
{
    private TaskRepositoryInterface $taskRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository
    )
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(
        string $projectName, string $name,
        string $description, string $startDate,
        string $endDate, string $status,
        array $consultantsEmails
    ): JsonResponse
    {

        if(!$this->taskRepository->checkIfProjectExists($projectName)){
            return new JsonResponse(['error' => 'El proyecto no existe'], 404);
        }
        $project = $this->taskRepository->findProjectByName($projectName);

        /*if($this->taskRepository->checkIfTaskExists($name, $project)){
            return new JsonResponse(['error' => 'La tarea ya existe'], 404);
        }*/

        /*if (!$this->taskRepository->checkDates($startDate, $endDate)) {
            return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 404);
        }*/

        $task = new Task();
        $task->setProject($project);
        $task->setName($name);
        $task->setDescription($description);
        $task->setStartDate(new \DateTime($startDate));
        $task->setEndDate(new \DateTime($endDate));
        $task->setStatus(Status::from($status));
        foreach ($consultantsEmails as $consultantEmail) {
            $consultant = $this->taskRepository->findConsultantByEmail($consultantEmail);
            $project->addConsultant($consultant);
        }

        $this->taskRepository->add($task);

        return new JsonResponse([
            'message' => 'Task created successfully',
            'task' => TaskDTO::fromEntity($task),
        ], 201);
    }
}