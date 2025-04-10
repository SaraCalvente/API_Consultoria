<?php
declare(strict_types=1);

namespace App\Client\Application\Admin;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
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

    public function __invoke(
        string $email, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        $user = $this->userRepository->findUserByEmail($email);
        return $this->clientRepository->updateClient($user, $address, $phoneNumber);
    }
}