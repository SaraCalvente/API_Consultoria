<?php

namespace App\Client\Application;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Project\Domain\Project;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    public function registerClient(
        string $email, string $password,
        string $name, string $surnames,
        string $address, string $phoneNumber): JsonResponse
    {
        $user = new User();
        $user->setEmail(new EmailValueObject($email));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CLIENT']);
        $this->entityManager->persist($user);

        $client = new Client();
        $client->setUser($user);
        $client->setName($name);
        $client->setSurnames($surnames);
        $client->setPhoneNumber($phoneNumber);
        $client->setAddress($address);
        $this->entityManager->persist($client);

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'User registered successfully',
            'user_id' => $user->getId(),
            'client_id' => $client->getId(),
            'email' => $user->getEmail(),
            'name' => $client->getName(),
            'roles' => $user->getRoles(),
        ], 201);
    }

    public function getClient(int $userId): array
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $userId]);

        if (!$client) {
            throw new \Exception('Client not found');
        }

        return [
            'client_id' => $client->getId(),
            'name' => $client->getName(),
            'surnames' => $client->getSurnames(),
            'address' => $client->getAddress(),
            'phone_number' => $client->getPhoneNumber(),
        ];
    }

    public function getAllClients(): JsonResponse
    {
        $clients = $this->entityManager->getRepository(Client::class)->findAll();

        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = [
                'client_id' => $client->getId(),
                'user_id' => $client->getUser()->getId(),
                'email' => $client->getUser()->getEmail(),
                'name' => $client->getName(),
                'surnames' => $client->getSurnames(),
                'address' => $client->getAddress(),
                'phone_number' => $client->getPhoneNumber(),
            ];
        }

        return new JsonResponse($clientData, 200);
    }

    public function updateClient(
        int $userId, string $password = null,
        string $address = null, string $phoneNumber = null
    ): JsonResponse {
        try {
            $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $userId]);
            if (!$client) {
                return new JsonResponse(['error' => 'Client not found'], 404);
            }
            $user = $client->getUser();

            if ($address !== null) {
                $client->setAddress($address);
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


    public function deleteClient(int $userId): JsonResponse
    {
        try {
            $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $userId]);

            if (!$client) {
                return new JsonResponse(['error' => 'Client not found'], 404);
            }
            $projects = $this->entityManager->getRepository(Project::class)->findBy(['client' => $client->getId()]);

            if (count($projects) > 0) {
                throw new \Exception('Cannot delete client because there are associated projects.');
            }

            $this->entityManager->remove($client);
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Client and associated user deleted successfully'], 200);


        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }



}
