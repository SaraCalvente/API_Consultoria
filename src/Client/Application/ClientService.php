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
        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $email])) {
            return new JsonResponse(['error' => 'Email is already registered'], 409);
        }

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

    public function getClient(int $userId): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $userId]);

        if (!$client) {
            throw new \Exception('Client not found');
        }

        return new JsonResponse( [
            'client_id' => $client->getId(),
            'name' => $client->getName(),
            'surnames' => $client->getSurnames(),
            'address' => $client->getAddress(),
            'phone_number' => $client->getPhoneNumber(),
        ], 200);
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
        int $userId, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        return $this->modifyClient($userId, null, $address, $phoneNumber);
    }

    public function adminUpdateClient(
        string $email, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        return $this->modifyClient(null, $email, $address, $phoneNumber);
    }

    private function modifyClient(?int $userId, ?string $email, ?string $address, ?string $phoneNumber): JsonResponse
    {
        $criteria = $email ? ['email' => $email] : ['id' => $userId];
        $user = $this->entityManager->getRepository(User::class)->findOneBy($criteria);

        if (!$user) { return new JsonResponse(['error' => 'User not found'], 404);}

        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);

        if (!$client) { return new JsonResponse(['error' => 'Client not found'], 404); }

        if ($address !== null) {
            $client->setAddress($address);
        }
        if ($phoneNumber !== null) {
            $client->setPhoneNumber($phoneNumber);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Client updated successfully',
            'user_id' => $user->getId(),
            'client_id' => $client->getId(),
            'email' => $user->getEmail(),
            'address' => $client->getAddress(),
            'phone_number' => $client->getPhoneNumber(),
        ], 200);
    }

    public function deleteClient(int $userId): JsonResponse
    {
        return $this->removeClient($userId, null);
    }

    public function adminDeleteClient(string $email): JsonResponse
    {
        return $this->removeClient(null, $email);
    }

    private function removeClient(?int $userId, ?string $email): JsonResponse
    {
        $criteria = $email ? ['email' => $email] : ['id' => $userId];
        $user = $this->entityManager->getRepository(User::class)->findOneBy($criteria);

        if (!$user) { return new JsonResponse(['error' => 'User not found'], 404); }

        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $user]);

        if (!$client) { return new JsonResponse(['error' => 'Client not found'], 404);}

        $projects = $this->entityManager->getRepository(Project::class)->findBy(['client' => $client->getId()]);

        if (count($projects) > 0) {
            return new JsonResponse(['error' => 'Cannot delete client because there are associated projects.'], 400);
        }

        $this->entityManager->remove($client);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Client and associated user deleted successfully'], 200);
    }





}
