<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryDeleteByUserService
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
        $projects = $this->projectRepository->checkIfClientHasProjects($client);
        if ($projects == null) {
            return $this->clientRepository->deleteClient($client);
        }
        return $projects;
    }
}