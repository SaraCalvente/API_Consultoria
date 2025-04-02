<?php

namespace App\Project\Application\Project;

use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\Status;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectUpdateByNameService
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
        string $name, string $description = null, string $status = null, string $endDate = null,
        array  $addConsultantsEmails = null, array $erraseConsultantsEmails = null
    ): JsonResponse {
        $project = $this->projectRepository->findProjectByName($name);

        if ($description !== null) {
            $project->setDescription($description);
        }
        if ($status !== null) {
            $project->setStatus(Status::from($status));
        }
        $startDate = $project->getStartDate()->format('Y-m-d');
        try {
            $this->projectRepository->checkDates($startDate, $endDate);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        if ($endDate !== null) {
            if (!$this->projectRepository->checkDates($startDate, $endDate)) {
                return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 404);
            }
            $project->setEndDate(new \DateTime($endDate));
        }
        if ($addConsultantsEmails !== null) {
            foreach ($addConsultantsEmails as $consultantEmail) {
                $consultant = $this->projectRepository->findConsultantByEmail($consultantEmail);
                if (!$project->getConsultant()->contains($consultant)) {
                    $project->addConsultant($consultant);
                }
            }
        }
        if ($erraseConsultantsEmails !== null) {
            foreach ($erraseConsultantsEmails as $consultantEmail) {
                $consultant = $this->projectRepository->findConsultantByEmail($consultantEmail);
                if ($project->getConsultant()->contains($consultant)) {
                    $project->removeConsultant($consultant);
                }
            }
        }

        $this->projectRepository->save();

        return new JsonResponse([
            'message' => 'Project updated successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 200);

    }
}