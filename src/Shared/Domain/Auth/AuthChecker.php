<?php

namespace App\Shared\Domain\Auth;


use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class AuthChecker
{
    private UserRepositoryInterface $userRepository;


    public function __construct(
        UserRepositoryInterface $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }
    public function getAuthenticated(Security $security): User
    {
        $userId = $security->getUser();
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        return $user;
    }
}