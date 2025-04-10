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

class TaskUpdateByNameAndProjectService
{
    private TaskRepositoryInterface $taskRepository;
    private ProjectRepositoryInterface $projectRepository;
    private UserRepositoryInterface $userRepository;
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ProjectRepositoryInterface $projectRepository,
        UserRepositoryInterface $userRepository,
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->consultantRepository = $consultantRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(
        string $name, string $projectName, string $description = null, string $status = null, string $endDate = null,
        array  $addConsultantsEmails = null, array $erraseConsultantsEmails = null
    ): JsonResponse {

        $project = $this->projectRepository->findProjectByName($projectName);
        $task = $this->taskRepository->findTaskFromProject($name, $project);

        if ($description !== null) {
            $task->setDescription($description);
        }
        if ($status !== null) {
            $task->setStatus(Status::from($status));
        }
        $startDate = $task->getStartDate()->format('Y-m-d');
        try {
            $this->taskRepository->checkDates($startDate, $endDate);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        if ($endDate !== null) {
            if (!$this->taskRepository->checkDates($startDate, $endDate)) {
                return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 404);
            }
            $project->setEndDate(new \DateTime($endDate));
        }
        if ($addConsultantsEmails !== null) {
            $this->updateTaskConsultants($task, $addConsultantsEmails, true);
        }

        if ($erraseConsultantsEmails !== null) {
            $this->updateTaskConsultants($task, $erraseConsultantsEmails, false);
        }

        $this->taskRepository->saveTask();

        return new JsonResponse([
            'message' => 'Task updated successfully',
            'project' => TaskDTO::fromEntity($task),
        ], 200);

    }

    private function updateTaskConsultants(Task $task, array $consultantsEmails, bool $add): void
    {
        foreach ($consultantsEmails as $consultantEmail) {
            $consultantUser = $this->userRepository->findUserByEmail($consultantEmail);
            $consultant = $this->consultantRepository->findConsultantByUser($consultantUser);

            if ($add) {
                if (!$task->getConsultants()->contains($consultant)) {
                    $task->addConsultant($consultant);
                }
            } else {
                if ($task->getConsultants()->contains($consultant)) {
                    $task->removeConsultant($consultant);
                }
            }
        }
    }
}