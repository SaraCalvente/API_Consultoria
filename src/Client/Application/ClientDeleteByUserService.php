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

    public function __invoke(User $user): void
    {
        $client = $this->clientRepository->findClientByUser($user);

        if ($this->projectRepository->checkIfClientHasProjects($client)) {
            throw new \DomainException('Cannot delete client because there are associated projects.');
        }

        $this->clientRepository->deleteClient($client);
    }

}