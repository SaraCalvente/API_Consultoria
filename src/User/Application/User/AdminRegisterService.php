<?php

namespace App\User\Application\User;

use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminRegisterService
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepositoryInterface $repository;


    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserRepositoryInterface     $repository
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->repository = $repository;
    }

    public function __invoke(array $data): JsonResponse
    {
        if($this->repository->checkIfUserExists($data['email'])){
            return new JsonResponse([
                'error' => 'User ' . $data['email'] . ' already exists',

            ], 402);
        }
        $user = new User();
        $user->setEmail(new EmailValueObject($data['email']));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);
        $this->repository->add($user);

        return new JsonResponse([
            'message' => 'Admin registered successfully',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], 201);
    }


}