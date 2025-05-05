<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\ClientDeleteDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ClientDeleteByEmailService
{
    public function __construct(
        private ClientRepositoryInterface  $clientRepository,
        private UserRepositoryInterface    $userRepository,
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function __invoke(ClientDeleteDTO $dto): void
    {
        $user = $this->userRepository->findUserByEmailOrFail($dto->email);
        $client = $this->clientRepository->findClientByUser($user);

        $hasProjects = $this->projectRepository->checkIfClientHasProjects($client);

        if ($hasProjects) {
            throw new DomainException("Client cannot be deleted due to associated projects.");
        }

        $this->clientRepository->deleteClient($client);
    }
}