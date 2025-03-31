<?php

namespace App\Shared\Domain\Auth;


use Symfony\Component\Security\Core\Security;

class AuthChecker
{
    public function getAuthenticatedUserId(Security $security): int
    {
        $user = $security->getUser();
        if (!$user) {
            throw new \Exception('User not authenticated');
        }
        return $user->getId();
    }
}