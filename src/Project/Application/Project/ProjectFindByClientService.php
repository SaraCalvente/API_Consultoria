<?php

namespace App\Project\Application\Project;

use App\Client\Domain\Client;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\ProjectDTO;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectFindByClientService
{
    private ProjectRepositoryInterface $projectRepository;
    private ClientRepositoryInterface $clientRepository;
    private ConsultantRepositoryInterface $consultantRepository;



    public function __construct(
        ProjectRepositoryInterface    $projectRepository,
        ClientRepositoryInterface     $clientRepository,
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->projectRepository = $projectRepository;
        $this->clientRepository = $clientRepository;
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        $client = $this->clientRepository->findClientByUser($user);
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if (!$client && !$consultant) {
            return new JsonResponse(['error' => 'User has no associated projects'], 404);
        }

        $projects = $client ? $this->projectRepository->findProjectByClient($client): $consultant->getProject()->toArray();
        return new JsonResponse([
            'message' => 'Projects retrieved successfully',
            $client ? 'current_client_id' : 'current_consultant_id' => $client ? $client->getId() : $consultant->getId(),
            'projects' => array_map(fn($project) => ProjectDTO::fromEntity($project), $projects),
        ], 200);
    }

}