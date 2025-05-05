<?php
declare(strict_types=1);

namespace App\Client\Application;


use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoClientsFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ClientGetAllService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {}

    /**
     * @return ClientDTO[]
     *
     * @throws NoClientsFoundException if no clients are found.
     */
    public function __invoke(): array
    {
        $clients = $this->clientRepository->findAllClients();
        $clientData = [];

        foreach ($clients as $client) {
            $clientData[] = ClientDTO::fromEntity($client);
        }

        if (empty($clientData)) {
            throw new NoClientsFoundException();
        }

        return $clientData;
    }
}