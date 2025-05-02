<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByEmailService
{
    public function __construct(private ClientRepositoryInterface  $clientRepository, private UserRepositoryInterface    $userRepository)
    {
    }

    public function __invoke( array $data
    ): JsonResponse {
        $user = $this->userRepository->findUserByEmailOrFail($data['email']);
        $address = $data['address'] ?? null;
        $phone = $data['phoneNumber'] ?? null;

        if ($data === [] || $address === null && $phone === null) {
            throw new NoDataToUpdateException();
        }
        return $this->clientRepository->updateClient($user, $address, $phone);
    }
}