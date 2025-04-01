<?php

namespace App\Client\Application;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientRegisterService
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

    public function __invoke(
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

}