<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\ClientByEmailDTO;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;

final readonly class ClientGetByEmailService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    public function __invoke(ClientByEmailDTO $request): ClientDTO
    {
        $user = $this->userRepository->findUserByEmailOrFail($request->email);
        $client = $this->clientRepository->findClientByUser($user);

        return ClientDTO::fromEntity($client);
    }
}