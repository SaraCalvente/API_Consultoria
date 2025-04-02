<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteByUserService
{
    private ClientRepositoryInterface $clientRepository;
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ClientRepositoryInterface  $clientRepository,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->clientRepository = $clientRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        $client = $this->clientRepository->findClientByUser($user);
        $projects = $this->projectRepository->findProjectByClient($client);

        if (count($projects) > 0) {
            $projectDetails = array_map(fn($p) => ['id' => $p->getId(), 'name' => $p->getName()], $projects);

            return new JsonResponse([
                'error' => 'Cannot delete client because there are associated projects.',
                'projects' => $projectDetails
            ], 400);
        }
        return $this->clientRepository->deleteClient($client);
    }
}