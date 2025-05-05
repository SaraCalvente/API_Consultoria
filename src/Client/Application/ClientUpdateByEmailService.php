<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\ClientDTO;
use App\Client\Domain\ClientUpdateByEmailDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ClientUpdateByEmailService
{
    public function __construct(
        private ClientRepositoryInterface  $clientRepository,
        private UserRepositoryInterface    $userRepository)
    {}

    public function __invoke(ClientUpdateByEmailDTO $data): ClientDTO
    {
        $user = $this->userRepository->findUserByEmailOrFail($data->email);

        if ($data->address === null && $data->phoneNumber === null) {
            throw new NoDataToUpdateException();
        }

        $updatedClient = $this->clientRepository->updateClient(
            $user,
            $data->address,
            $data->phoneNumber
        );

        return ClientDTO::fromEntity($updatedClient);
    }
}