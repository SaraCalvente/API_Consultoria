<?php

namespace App\User\Application;

use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;


    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
    }

    public function loginUser(string $email, string $password): JsonResponse{
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if(!$user){
            throw new BadCredentialsException('Invalid email or password');
        }

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

    public function getAllUsers(): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

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

