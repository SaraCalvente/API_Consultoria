<?php

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteByEmailService
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        return $this->clientRepository->removeClient(['email' => $email]);
    }
}