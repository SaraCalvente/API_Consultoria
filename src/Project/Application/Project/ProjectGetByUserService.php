<?php

namespace App\Project\Application\Project;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Project\Domain\Project\ProjectDTO;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectGetByUserService
{
    public function __construct(private ProjectRepositoryInterface    $projectRepository, private ClientRepositoryInterface     $clientRepository, private ConsultantRepositoryInterface $consultantRepository)
    {
    }

    public function __invoke(User $user): JsonResponse
    {
        $client = $this->clientRepository->checkIfClientExists($user) ? $this->clientRepository->findClientByUser($user) : null;
        $consultant = $this->consultantRepository->checkIfConsultantExists($user) ? $this->consultantRepository->findConsultantByUser($user) : null;

       if (!$client && !$consultant) {
            return new JsonResponse(['error' => 'User has no associated projects'], 404);
       }
       $projects = $client instanceof \App\Client\Domain\Client ? $this->projectRepository->findProjectByClient($client) : $consultant->getProject()->toArray();

        return new JsonResponse([
            'message' => 'Projects retrieved successfully',
            $client instanceof \App\Client\Domain\Client ? 'current_client_id' : 'current_consultant_id' => $client instanceof \App\Client\Domain\Client ? $client->getId() : $consultant->getId(),
            'projects' => array_map(fn($project) => ProjectDTO::fromEntity($project), $projects),
        ], 200);
    }

}