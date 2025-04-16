<?php
declare(strict_types=1);

namespace App\Client\Application\Admin;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClientUpdateByEmailService
{
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        ClientRepositoryInterface  $clientRepository,
        UserRepositoryInterface    $userRepository,
    )
    {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke( array $data
    ): JsonResponse {
        $user = $this->userRepository->findUserByEmail($data['email']);
        $address = $data['address'] ?? null;
        $phone = $data['phoneNumber'] ?? null;

        if (empty($data) || $address === null && $phone === null) {
            throw new NoDataToUpdateException();
        }
        return $this->clientRepository->updateClient($user, $address, $phone);
    }
}