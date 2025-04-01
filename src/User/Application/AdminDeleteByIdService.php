<?php

namespace App\User\Application;

use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminDeleteByIdService
{
    private UserRepositoryInterface $repository;


    public function __construct(
        UserRepositoryInterface     $repository
    ) {
        $this->repository = $repository;
    }

    public function __invoke(int $id): JsonResponse
    {
        $user = $this->repository->findUserById($id);
        $this->repository->remove($user);
        return new JsonResponse([
            'message' => 'Admin deleted successfully',
        ], 200);

    }
}