<?php

namespace App\Project\Application\Project;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project;
use App\Project\Domain\ProjectDTO;
use App\Project\Domain\Status;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectUpdateByNameService
{
    private ProjectRepositoryInterface $projectRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->consultantRepository = $consultantRepository;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function __invoke(
        string $name, string $description = null, string $status = null, string $endDate = null,
        array  $addConsultantsEmails = null, array $erraseConsultantsEmails = null
    ): JsonResponse {
        $project = $this->projectRepository->findProjectByName($name);
        if (!$this->projectRepository->checkIfProjectExists($project)) {
            return new JsonResponse(['error' => 'The project ' . $project->getName() . ' does not exist'], 400);
        }

        if ($description !== null) {
            $project->setDescription($description);
        }
        if ($status !== null) {
            $project->setStatus(Status::from($status));
        }
        $startDate = $project->getStartDate()->format('Y-m-d');

        $this->projectRepository->checkDates($startDate, $endDate);

        if ($endDate !== null) {
            if (!$this->projectRepository->checkDates($startDate, $endDate)) {
                return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 405);
            }
            $project->setEndDate(new \DateTime($endDate));
        }
        if ($addConsultantsEmails !== null) {
            $this->updateProjectConsultants($project, $addConsultantsEmails, true);
        }

        if ($erraseConsultantsEmails !== null) {
            $this->updateProjectConsultants($project, $erraseConsultantsEmails, false);
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
            $consultantUser = $this->userRepository->findUserByEmail($consultantEmail);
            $consultant = $this->consultantRepository->findConsultantByUser($consultantUser);

            if ($add) {
                if (!$project->getConsultant()->contains($consultant)) {
                    $project->addConsultant($consultant);
                }
            } else {
                if ($project->getConsultant()->contains($consultant)) {
                    $project->removeConsultant($consultant);
                }
            }
        }
    }
}