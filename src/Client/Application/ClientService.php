<?php

namespace App\Client\Application;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Project\Domain\Project;
use App\Shared\Domain\Exception\ClientNotFoundException;
use App\Shared\Domain\ValueObject\StringValueObject;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ClientService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private ClientRepositoryInterface $clientRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ClientRepositoryInterface $clientRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->clientRepository = $clientRepository;
    }


    public function registerClient(
        string $email, string $password,
        string $name, string $surnames,
        string $address, string $phoneNumber): JsonResponse
    {
        if(!$this->clientRepository->checkIfUserExists($email)){
            return new JsonResponse([
                'error' => 'El cliente ya existe',

            ], 400);
        }

        $user = new User();
        $user->setEmail(new EmailValueObject($email));
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CLIENT']);
        $this->entityManager->persist($user);

        $client = new Client();
        $client
            ->setUser($user)
            ->setName($name)
            ->setSurnames($surnames)
            ->setPhoneNumber($phoneNumber)
            ->setAddress($address);

        $this->clientRepository->add($client);

        return new JsonResponse([
            'message' => 'Cliente registrado correctamente',
            'client' => ClientDTO::fromEntity($client)
        ], 201);

    }

    public function getClient(int $userId): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->findOneBy(['user' => $userId]);

        if (!$client) {
            throw new ClientNotFoundException();
        }

        return new JsonResponse(ClientDTO::fromEntity($client));
    }

    public function getAllClients(): JsonResponse
    {
        $clients = $this->entityManager->getRepository(Client::class)->findAll();

        $clientData = [];
        foreach ($clients as $client) {
            $clientData[] = ClientDTO::fromEntity($client);
        }

        return new JsonResponse($clientData, 200);
    }

    public function updateClientById(
        int $userId, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        return $this->modifyClient(['id' => $userId], $address, $phoneNumber);
    }

    public function updateClientByEmail(
        string $email, ?string $address = null, ?string $phoneNumber = null
    ): JsonResponse {
        return $this->modifyClient(['email' => $email], $address, $phoneNumber);
    }

    private function modifyClient(array $criteria, ?string $address, ?string $phoneNumber): JsonResponse
    {
        [, $client] = $this->findUserAndClient($criteria);


        if ($address !== null) {
            $client->setAddress($address);
        }
        if ($phoneNumber !== null) {
            $client->setPhoneNumber($phoneNumber);
        }

        $this->entityManager->flush();

        return new JsonResponse(ClientDTO::fromEntity($client));
    }

    public function deleteClientById(int $userId): JsonResponse
    {
        return $this->removeClient(['id' => $userId]);
    }

    public function deleteClientByEmail(string $email): JsonResponse
    {
        return $this->removeClient(['email' => $email]);
    }

    private function removeClient(array $criteria): JsonResponse
    {
        [$user, $client] = $this->findUserAndClient($criteria);

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
