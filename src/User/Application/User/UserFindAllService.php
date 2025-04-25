<?php

namespace App\User\Application\User;

use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserFindAllService
{
    private UserRepositoryInterface $repository;


    public function __construct(
        UserRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    public function __invoke(): JsonResponse
    {
        $users = $this->repository->findAllUsers();

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($userData, 200);
    }
}