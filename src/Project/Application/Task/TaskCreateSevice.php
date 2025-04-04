<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\Project;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\Status;
use App\Project\Domain\Task;
use App\Project\Domain\TaskDTO;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskCreateSevice
{
    private TaskRepositoryInterface $taskRepository;
    private ProjectRepositoryInterface $projectRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ProjectRepositoryInterface $projectRepository,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
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

        if(!$this->projectRepository->checkIfProjectExists($projectName)){
            return new JsonResponse(['error' => 'El proyecto no existe'], 404);
        }

        $project = $this->projectRepository->findProjectByName($projectName);

        if($this->taskRepository->checkIfTaskFromProjectExists($name, $project)){
            return new JsonResponse(['error' => 'La tarea ya existe'], 404);
        }

        if (!$this->taskRepository->checkDates($startDate, $endDate)) {
            return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 404);
        }

        $task = new Task();
        $task->setProject($project);
        $task->setName($name);
        $task->setDescription($description);
        $task->setStartDate(new \DateTime($startDate));
        $task->setEndDate(new \DateTime($endDate));
        $task->setStatus(Status::from($status));
        foreach ($consultantsEmails as $consultantEmail) {
            $consultantUser = $this->userRepository->findUserByEmail($consultantEmail);
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