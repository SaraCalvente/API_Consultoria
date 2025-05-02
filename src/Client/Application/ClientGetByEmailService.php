<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ClientGetByEmailService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private UserRepositoryInterface $userRepository)
    {}

    public function __invoke(string $email): JsonResponse
    {
        $user = $this->userRepository->findUserByEmailOrFail($email);
        $client = $this->clientRepository->findClientByUser($user);
        return new JsonResponse(ClientDTO::fromEntity($client));
    }
}