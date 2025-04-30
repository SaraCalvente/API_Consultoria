<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteByEmailService
{
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ClientRepositoryInterface  $clientRepository,
        UserRepositoryInterface    $userRepository,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(array $data): JsonResponse
    {
        if (!$data || !$data['email']){
            return new JsonResponse(['error' => 'No email provided.'], 400);
        }
        $user = $this->userRepository->findUserByEmail($data['email']);
        $client = $this->clientRepository->findClientByUser($user);
        $projects = $this->projectRepository->checkIfClientHasProjects($client);
        if (!$projects) {
            return $this->clientRepository->deleteClient($client);
        }
        return $projects;

    }
}