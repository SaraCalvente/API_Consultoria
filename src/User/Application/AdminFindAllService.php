<?php

namespace App\User\Application;

use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFindAllService
{
    private UserRepositoryInterface $repository;


    public function __construct(
        UserRepositoryInterface     $repository
    ) {
        $this->repository = $repository;
    }

    public function __invoke(): JsonResponse
    {
        $admins = $this->repository->getAllAdmins();

        $adminsData = [];
        foreach ($admins as $admin) {
            $adminsData[] = [
                'user_id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'roles' => $admin->getRoles(),
            ];
        }

        return new JsonResponse([
            'message' => 'Admins retrieved successfully',
            'All admins' => $adminsData,
            200]);
    }
}