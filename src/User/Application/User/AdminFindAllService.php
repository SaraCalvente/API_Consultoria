<?php

namespace App\User\Application\User;

use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminFindAllService
{
    public function __construct(private UserRepositoryInterface     $repository)
    {
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