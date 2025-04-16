<?php
declare(strict_types=1);

namespace App\Client\Application\Admin;


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

        return new JsonResponse($clientData, 200);
    }
}