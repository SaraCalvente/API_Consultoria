<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientGetByEmailService
{
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);
        if(!$user){
            return new JsonResponse(['error' => 'No user found'], 404);
        }
        $client = $this->clientRepository->findClientByUser($user);
        return new JsonResponse(ClientDTO::fromEntity($client));
    }
}