<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityHistoryFindByUserService
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        $client = $this->clientRepository->findClientByUser($user);
        return new JsonResponse(ClientDTO::fromEntity($client));
    }
}