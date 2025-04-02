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

class ProjectCreateService
{

    private ProjectRepositoryInterface $projectRepository;
    private UserRepositoryInterface $userRepository;
    private ClientRepositoryInterface $clientRepository;
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        UserRepositoryInterface $userRepository,
        ClientRepositoryInterface $clientRepository,
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->consultantRepository = $consultantRepository;
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

        $clientUser = $this->userRepository->findUserByEmail($clientEmail);
        $client = $this->clientRepository->findClientByUser($clientUser);

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
            $consultantUser = $this->userRepository->findUserByEmail($consultantEmail);
            $consultant = $this->consultantRepository->findConsultantByUser($consultantUser);
            $project->addConsultant($consultant);
        }

        $this->projectRepository->add($project);

        return new JsonResponse([
            'message' => 'Project created successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 201);
    }
}