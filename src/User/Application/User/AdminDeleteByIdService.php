<?php

namespace App\User\Application\User;

use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class AdminDeleteByIdService
{
    public function __construct(
        private UserRepositoryInterface     $repository
    )
    {}

    public function __invoke(User $user): JsonResponse
    {
        $user = $this->repository->findUserByIdOrFail($user->getId());
        $this->repository->remove($user);
        return new JsonResponse([
            'message' => 'Admin deleted successfully',
        ], 200);

    }
}