<?php

namespace App\Shared\Domain\Auth;


use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class AuthChecker
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }
    public function getAuthenticated(Security $security): User
    {
        $userId = $security->getUser();
        if (!$userId instanceof \Symfony\Component\Security\Core\User\UserInterface){
            throw new \Exception('No user logged in');
        }
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        return $user;
    }
}