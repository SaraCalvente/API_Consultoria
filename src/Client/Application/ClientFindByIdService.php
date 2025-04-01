<?php

namespace App\Client\Application;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\ClientNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFindByIdService
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(int $id): JsonResponse
    {
        $client = $this->clientRepository->findClientById($id);
        return new JsonResponse(ClientDTO::fromEntity($client));
    }
}