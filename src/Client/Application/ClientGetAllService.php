<?php
declare(strict_types=1);

namespace App\Client\Application;


use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientGetAllService
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository,
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(): JsonResponse
    {
        $clients = $this->clientRepository->findAllClients();

        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = ClientDTO::fromEntity($client);
        }
        if (empty($clientData)) {
            return new JsonResponse(['error' => 'There are no activities'], 404);
        }

        return new JsonResponse($clientData, 200);
    }
}