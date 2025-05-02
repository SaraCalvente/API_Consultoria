<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ClientDeleteByUserService
{
    public function __construct(
        private ClientRepositoryInterface  $clientRepository,
        private ProjectRepositoryInterface $projectRepository)
    {}

    public function __invoke(User $user): JsonResponse
    {
        $client = $this->clientRepository->findClientByUser($user);
        $projects = $this->projectRepository->checkIfClientHasProjects($client);
        if (!$projects instanceof JsonResponse) {
            return $this->clientRepository->deleteClient($client);
        }
        return $projects;
    }
}