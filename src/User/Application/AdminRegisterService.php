<?php

namespace App\User\Application;

use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __invoke(string $email, string $password): JsonResponse
    {
        if(!$this->repository->checkIfUserExists($email)){
            return new JsonResponse([
                'error' => 'El usuario ya existe',

            ], 400);
        }
        $user = new User();
        $user->setEmail(new EmailValueObject($email));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);
        $this->repository->add($user);

        return new JsonResponse([
            'message' => 'User registered successfully',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], 201);
    }


}