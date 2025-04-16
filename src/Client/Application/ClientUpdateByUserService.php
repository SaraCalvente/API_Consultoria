<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use function PHPUnit\Framework\isEmpty;

class ClientUpdateByUserService
{
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(
        User $user, array $data
    ): JsonResponse {
        $address = $data['address'] ?? null;
        $phone = $data['phoneNumber'] ?? null;

        if (empty($data) || $address === null && $phone === null) {
            throw new NoDataToUpdateException();
        }
        return $this->clientRepository->updateClient($user, $address, $phone);
    }
}