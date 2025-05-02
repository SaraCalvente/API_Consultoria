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

final readonly class TaskUpdateByNameAndProjectService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private ProjectRepositoryInterface $projectRepository,
        private UserRepositoryInterface $userRepository,
        private ConsultantRepositoryInterface $consultantRepository
    )
    {}

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke( array $data
    ): JsonResponse {

        $project = $this->projectRepository->findProjectByName($data['projectName']);
        $task = $this->taskRepository->findTaskFromProject($data['name'], $project);

        if ($data['description'] !== null) {
            $task->setDescription($data['description']);
        }
        if ($data['status'] !== null) {
            $task->setStatus(Status::from($data['status']));
        }
        $startDate = $task->getStartDate()->format('Y-m-d');
        try {
            $this->taskRepository->checkDates($startDate, $data['endDate']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        if ($data['endDate'] !== null) {
            if (!$this->taskRepository->checkDates($startDate, $data['endDate'])) {
                return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 404);
            }
            $project->setEndDate(new \DateTime($data['endDate']));
        }
        if ($data['addConsultantsEmails'] !== null) {
            $this->updateTaskConsultants($task, $data['addConsultantsEmails'], true);
        }

        if ($data['erraseConsultantsEmails'] !== null) {
            $this->updateTaskConsultants($task, $data['erraseConsultantsEmails'], false);
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
            $consultantUser = $this->userRepository->findUserByEmailOrFail($consultantEmail);
            $consultant = $this->consultantRepository->findConsultantByUser($consultantUser);

            if ($add) {
                if (!$task->getConsultants()->contains($consultant)) {
                    $task->addConsultant($consultant);
                }
            } elseif ($task->getConsultants()->contains($consultant)) {
                $task->removeConsultant($consultant);
            }
        }
    }
}