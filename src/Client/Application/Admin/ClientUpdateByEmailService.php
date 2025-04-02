<?php
declare(strict_types=1);

namespace App\Client\Application\Admin;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByEmailService
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

    public function __invoke(
        string $email, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        $user = $this->userRepository->findUserByEmail($email);
        $client = $this->clientRepository->findClientByUser($user);
        $projects = $this->projectRepository->findProjectByClient($client);

        if (count($projects) > 0) {
            return new JsonResponse(['error' => 'Cannot delete client because there are associated projects.'], 400);
        }
        return $this->clientRepository->updateClient($user, $address, $phoneNumber);
    }
}