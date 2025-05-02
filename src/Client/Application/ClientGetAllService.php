<?php
declare(strict_types=1);

namespace App\Client\Application;


use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientGetAllService
{
    public function __construct(private ClientRepositoryInterface $clientRepository)
    {
    }

    public function __invoke(): JsonResponse
    {
        $clients = $this->clientRepository->findAllClients();

        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = ClientDTO::fromEntity($client);
        }
        if ($clientData === []) {
            return new JsonResponse(['error' => 'There are no activities'], 404);
        }

        return new JsonResponse($clientData, 200);
    }
}