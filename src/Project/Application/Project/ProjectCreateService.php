<?php

namespace App\Project\Application\Project;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\Project;
use App\Project\Domain\Project\ProjectDTO;
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
    public function __invoke( array $data ): JsonResponse
    {
        if($this->projectRepository->checkIfProjectExists($data['name'])){
            return new JsonResponse(['error' => 'A project with this name: (' . $data['name'] .') already exists'], 403);
        }


        if (!$this->projectRepository->checkDates($data['startDate'], $data['endDate'])) {
            return new JsonResponse(['error' => 'Invalid date range or format (Y-m-d)'], 405);
        }

        $clientUser = $this->userRepository->findUserByEmail($data['clientEmail']);
        $client = $this->clientRepository->findClientByUser($clientUser);

        $project = new Project();
        $project
            ->setClient($client)
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setStartDate(new \DateTime($data['startDate']));
        if ($data['endDate']) {
            $project->setEndDate(new \DateTime($data['endDate']));
        }
        $project->setStatus(Status::from($data['status']));

        foreach ($data['consultantsEmails'] as $consultantEmail) {
            $consultantUser = $this->userRepository->findUserByEmail($consultantEmail);
            $consultant = $this->consultantRepository->findConsultantByUser($consultantUser);
            $project->addConsultant($consultant);
        }

        $this->projectRepository->addProject($project);

        return new JsonResponse([
            'message' => 'Project created successfully',
            'project' => ProjectDTO::fromEntity($project),
        ], 201);
    }
}