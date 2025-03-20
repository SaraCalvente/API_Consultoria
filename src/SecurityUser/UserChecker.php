<?php

namespace App\SecurityUser;


use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {

    }

    public function checkPostAuth(UserInterface $user)
    {
//        Check if user is enabled/locked
//        if (!$user instanceof User) {
//            return;
//        }
//
//        // user account is expired, the user may be notified
//        if (!$user->getEnabled()) {
//            throw new HttpException(401, 'TR_USER_NOT_ENABLED');
//        }
    }
}