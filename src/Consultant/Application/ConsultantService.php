<?php
// src/Consultant/Application/ConsultantService.php

namespace App\Consultant\Application;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Profile;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConsultantService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function registerConsultant(string $email, string $password, string $name, string $surnames, string $profile): JsonResponse
    {
        $user = new User();
        $user->setEmail(new EmailValueObject($email));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CONSULTANT']);
        $this->entityManager->persist($user);

        $consultant = new Consultant();
        $consultant->setName($name);
        $consultant->setSurnames($surnames);
        $consultant->setProfile(Profile::from($profile));
        $consultant->setUser($user);
        $this->entityManager->persist($consultant);

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'User registered successfully',
            'user_id' => $user->getId(),
            'consultant_id' => $consultant->getId(),
            'email' => $user->getEmail(),
            'name' => $consultant->getName(),
            'profile' => $consultant->getProfile(),
            'roles' => $user->getRoles(),
        ], 201);
    }



    public function getConsultant(int $userId): array
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $userId]);

        if (!$consultant) {
            throw new \Exception('Consultant not found');
        }

        return [
            'consultant_id' => $consultant->getId(),
            'name' => $consultant->getName(),
            'email' => $consultant->getUser()->getEmail(),
            'surnames' => $consultant->getSurnames(),
            'profile' => $consultant->getProfile(),
        ];
    }

    public function getAllConsultants(): JsonResponse
    {
        $consultants = $this->entityManager->getRepository(Consultant::class)->findAll();

        $consultantData = [];
        foreach ($consultants as $consultant) {
            $consultantData[] = [
                'consultant_id' => $consultant->getId(),
                'user_id' => $consultant->getUser()->getId(),
                'email' => $consultant->getUser()->getEmail(),
                'name' => $consultant->getName(),
                'surnames' => $consultant->getSurnames(),
                'profile' => $consultant->getProfile(),
            ];
        }

        return new JsonResponse($consultantData, 200);
    }

    public function updateConsultant(
        int $userId, string $profile = null
    ): JsonResponse {
        try {
            $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $userId]);
            if (!$consultant) {
                return new JsonResponse(['error' => 'Client not found'], 404);
            }
            $user = $client->getUser();

            if ($profile !== null) {
                $client->s($address);
            }
            if ($phoneNumber !== null) {
                $client->setPhoneNumber($phoneNumber);
            }
            /*if ($password !== null) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }*/
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Client updated successfully',
                'user_id' => $user->getId(),
                'client_id' => $client->getId(),
                'email' => $user->getEmail(),
                'name' => $client->getName(),
                'surnames' => $client->getSurnames(),
                'address' => $client->getAddress(),
                'phone_number' => $client->getPhoneNumber()
            ], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteConsultant(int $userId): JsonResponse
    {
        try {
            $consultant = $this->entityManager->getRepository(Consultant::class)->findOneBy(['user' => $userId]);

            if (!$consultant) {
                return new JsonResponse(['error' => 'Consultant not found'], 404);
            }

            $projects = $consultant->getProject();

            if (!$projects) {
                throw new \Exception('Cannot delete consultant because there are associated projects.');
            }

            $this->entityManager->remove($consultant);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Consultant and associated user deleted successfully'], 200);


        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
