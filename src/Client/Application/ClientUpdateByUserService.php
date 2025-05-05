<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\ClientDTO;
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

    public function __invoke(User $user, array $data): ClientDTO
    {
        $address = $data['address'] ?? null;
        $phone = $data['phone_number'] ?? null;

        if ($address === null && $phone === null) {
            throw new NoDataToUpdateException();
        }

        $client = $this->clientRepository->updateClient($user, $address, $phone);

        return ClientDTO::fromEntity($client);
    }
}