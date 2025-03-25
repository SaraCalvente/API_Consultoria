<?php

// src/Service/UserService.php

namespace App\User\Application;

use App\Client\Application\ClientService;
use App\Client\Domain\Client;
use App\Consultant\Application\ConsultantService;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Profile;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
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
    private ClientService $clientService;
    private ConsultantService $consultantService;


    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ClientService $clientService,
        ConsultantService  $consultantService
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->clientService = $clientService;
        $this->consultantService = $consultantService;
    }

    public function registerUser(string $email, string $password, string $userType, array $additionalData): JsonResponse
    {
        $user = new User();
        $user->setEmail(new EmailValueObject($email));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        if ($userType === 'consultant') {
            $user->setRoles(['ROLE_CONSULTANT']);
            $this->consultantService->createConsultantProfile($user, $additionalData);
        } elseif ($userType === 'client') {
            $user->setRoles(['ROLE_CLIENT']);
            $this->clientService->createClientProfile($user, $additionalData);
        } else {
            return new JsonResponse(['error' => 'Invalid user type'], 400);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'User registered successfully',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], 201);
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

