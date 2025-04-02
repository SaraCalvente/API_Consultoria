<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByUserService
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(
        User $user, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        return $this->clientRepository->updateClient($user, $address, $phoneNumber);
    }
}