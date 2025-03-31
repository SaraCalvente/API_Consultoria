<?php

namespace App\Project\Domain;

use App\Project\Domain\Project;

class ProjectDTO
{
    public static function fromEntity(Project $project): array
    {
        return [
            'project_id' => $project->getId(),
            'client_id' => $project->getClient()->getId(),
            'name' => $project->getName(),
            'description' => $project->getDescription(),
            'start_date' => $project->getStartDate()->format('Y-m-d'),
            'end_date' => $project->getEndDate()?->format('Y-m-d'),
            'status' => $project->getStatus(),
            'consultantsId' => array_map(fn($c) => $c->getId(), $project->getConsultant()->toArray())
        ];
    }
}