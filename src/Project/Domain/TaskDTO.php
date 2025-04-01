<?php

namespace App\Project\Domain;

class TaskDTO
{
    public static function fromEntity(Task $task): array
    {
        return [
            'task_id' => $task->getId(),
            'name' => $task->getName(),
            'description' => $task->getDescription(),
            'project_id' => $task->getProject()->getId(),
            'start_date' => $task->getStartDate()->format('Y-m-d'),
            'end_date' => $task->getEndDate()?->format('Y-m-d'),
            'status' => $task->getStatus(),
            'consultantsId' => array_map(fn($c) => $c->getId(), $task->getConsultants()->toArray())
        ];
    }
}