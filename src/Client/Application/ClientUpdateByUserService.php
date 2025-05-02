<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use function PHPUnit\Framework\isEmpty;

final readonly class ClientUpdateByUserService
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    )
    {}

    public function __invoke(
        User $user, array $data
    ): JsonResponse {
        $address = $data['address'] ?? null;
        $phone = $data['phoneNumber'] ?? null;

        if ($data === [] || $address === null && $phone === null) {
            throw new NoDataToUpdateException();
        }
        return $this->clientRepository->updateClient($user, $address, $phone);
    }
}