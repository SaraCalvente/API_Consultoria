<?php

namespace App\Project\Application\Project;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Project\ProjectDTO;
use App\Project\Domain\Status;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ProjectUpdateByNameService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private ConsultantRepositoryInterface $consultantRepository,
        private UserRepositoryInterface $userRepository
    )
    {}

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke( array $data
    ): JsonResponse {
        $project = $this->projectRepository->findProjectByName($data['name']);
        if (!$this->projectRepository->checkIfProjectExists($project->getName())) {
            return new JsonResponse(['error' => 'The project ' . $project->getName() . ' does not exist'], 400);
        }

        if ($data['description'] !== null) {
            $project->setDescription($data['description']);
        }
        if ($data['status'] !== null) {
            $project->setStatus(Status::from($data['status']));
        }
        $startDate = $project->getStartDate()->format('Y-m-d');

        $this->projectRepository->checkDates($startDate, $data['endDate']);

        if ($data['endDate'] !== null && !$this->projectRepository->checkDates($startDate, $data['endDate'])) {
            return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 405);
        }

        if ($data['endDate'] !== null) {
            $project->setEndDate(new \DateTime($data['endDate']));
        }

        if ($data['addConsultantsEmails'] !== null) {
            $this->updateProjectConsultants($project, $data['addConsultantsEmails'], true);
        }

        if ($data['erraseConsultantsEmails'] !== null) {
            $this->updateProjectConsultants($project, $data['erraseConsultantsEmails'], false);
        }

        $this->projectRepository->saveProject();

        return new JsonResponse([
            'message' => 'Project updated successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 200);

    }

    private function updateProjectConsultants(Project $project, array $consultantsEmails, bool $add): void
    {
        foreach ($consultantsEmails as $consultantEmail) {
            $consultantUser = $this->userRepository->findUserByEmailOrFail($consultantEmail);
            $consultant = $this->consultantRepository->findConsultantByUser($consultantUser);

            if ($add) {
                if (!$project->getConsultant()->contains($consultant)) {
                    $project->addConsultant($consultant);
                }
            } elseif ($project->getConsultant()->contains($consultant)) {
                $project->removeConsultant($consultant);
            }
        }
    }
}