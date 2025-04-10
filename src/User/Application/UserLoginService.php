<?php

namespace App\User\Application;

use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class UserLoginService
{
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;
    private UserRepositoryInterface $repository;


    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        UserRepositoryInterface $repository
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->repository = $repository;
    }

    public function __invoke(string $email, string $password): JsonResponse{
        $user = $this->repository->findUserByEmail($email);

        if(!$this->passwordHasher->isPasswordValid($user, $password)){
            throw new BadCredentialsException('Invalid email or password');
        }

        try {
            $token = $this->jwtManager->create($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Token generation failed: ' . $e->getMessage()], 500);
        }

        return new JsonResponse([
            'message' => 'User logged in successfully',
            'user_id' => $user->getId(),
            'token' => $token
        ], 200);
    }

}