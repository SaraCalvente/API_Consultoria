<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\Status;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectCreateService
{

    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(
        string $clientEmail, string $name,
        string $description, string $startDate,
        ?string $endDate, string $status,
        array $consultantsEmails
    ): JsonResponse
    {
        if($this->projectRepository->checkIfProjectExists($name)){
            return new JsonResponse(['error' => 'Ya existe un proyecto con este nombre'], 404);
        }


        if (!$this->projectRepository->checkDates($startDate, $endDate)) {
            return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 404);
        }

        $client = $this->projectRepository->findClientByEmail($clientEmail);

        $project = new Project();
        $project
            ->setClient($client)
            ->setName($name)
            ->setDescription($description)
            ->setStartDate(new \DateTime($startDate));
        if ($endDate) {
            $project->setEndDate(new \DateTime($endDate));
        }
        $project->setStatus(Status::from($status));

        foreach ($consultantsEmails as $consultantEmail) {
            $consultant = $this->projectRepository->findConsultantByEmail($consultantEmail);
            $project->addConsultant($consultant);
        }

        $this->projectRepository->add($project);

        return new JsonResponse([
            'message' => 'Project created successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 201);
    }
}