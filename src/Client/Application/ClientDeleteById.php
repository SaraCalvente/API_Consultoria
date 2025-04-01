<?php

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientDeleteById
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(int $userId): JsonResponse
    {
        return $this->clientRepository->removeClient(['id' => $userId]);
    }
}