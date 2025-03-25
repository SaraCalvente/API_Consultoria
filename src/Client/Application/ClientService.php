<?php

namespace App\Client\Application;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createClientProfile($user, array $data): void
    {
        $client = new Client();
        $client->setUserId($user);
        $client->setName($data['name']);
        $client->setSurnames($data['surnames']);
        $client->setAddress($data['address']);
        $client->setEmail($user->getEmail());
        $client->setPhoneNumber($data['phoneNumber']);

        $this->entityManager->persist($client);
    }

    public function getClientById(int $id): array
    {
        $client = $this->entityManager->getRepository(Client::class)->find($id);

        if (!$client) {
            throw new \Exception('Client not found');
        }

        return [
            'client_id' => $client->getId(),
            'name' => $client->getName(),
            'surnames' => $client->getSurnames(),
            'address' => $client->getAddress(),
            'phone_number' => $client->getPhoneNumber(),
        ];
    }

    public function getAllClients(): JsonResponse
    {
        $clients = $this->entityManager->getRepository(Client::class)->findAll();

        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = [
                'client_id' => $client->getId(),
                'name' => $client->getName(),
                'surnames' => $client->getSurnames(),
                'address' => $client->getAddress(),
                'email' => $client->getEmail(),
                'phone_number' => $client->getPhoneNumber(),
            ];
        }

        return new JsonResponse($clientData, 200);
    }

    public function deleteClient(int $id): void
    {
        $client = $this->entityManager->getRepository(Client::class)->find($id);

        if (!$client) {
            throw new \Exception('Consultant not found');
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();
    }
}
