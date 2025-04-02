<?php
declare(strict_types=1);

namespace App\Project\Application\Task;

use App\Project\Domain\Model\TaskRepositoryInterface;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\Status;
use App\Project\Domain\TaskDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

class TareaUpdateByNameService
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
        string $name, string $projectName, string $description = null, string $status = null, string $endDate = null,
        array  $addConsultantsEmails = null, array $erraseConsultantsEmails = null
    ): JsonResponse {

        $project = $this->taskRepository->findProjectByName($projectName);
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
            foreach ($addConsultantsEmails as $consultantEmail) {
                $consultant = $this->taskRepository->findConsultantByEmail($consultantEmail);
                if (!$task->getConsultants()->contains($consultant)) {
                    $task->addConsultant($consultant);
                }
            }
        }
        if ($erraseConsultantsEmails !== null) {
            foreach ($erraseConsultantsEmails as $consultantEmail) {
                $consultant = $this->taskRepository->findConsultantByEmail($consultantEmail);
                if ($task->getConsultants()->contains($consultant)) {
                    $task->removeConsultant($consultant);
                }
            }
        }

        $this->taskRepository->save();

        return new JsonResponse([
            'message' => 'Task updated successfully',
            'project' => TaskDTO::fromEntity($task),
        ], 200);

    }
}