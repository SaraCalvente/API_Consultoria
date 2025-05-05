<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ClientGetByUserService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    )
    {}

    public function __invoke(User $user): ClientDTO
    {
        $client = $this->clientRepository->findClientByUser($user);

        return ClientDTO::fromEntity($client);
    }
}