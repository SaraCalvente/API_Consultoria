<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientRegisterService
{
    private UserPasswordHasherInterface $passwordHasher;
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        ClientRepositoryInterface   $clientRepository,
        UserRepositoryInterface     $userRepository
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(
        string $email, string $password,
        string $name, string $surnames,
        string $address, string $phoneNumber): JsonResponse
    {
        if ($this->userRepository->checkIfUserExists($email)){
            $user = $this->userRepository->findUserByEmail($email);
            return new JsonResponse([
                'error' => 'User ' . $user->getEmail() . ' already exists as ' . implode(', ', $user->getRoles()),

            ], 403);
        }

        $user = new User();
        $user->setEmail(new EmailValueObject($email));
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CLIENT']);

        $client = new Client();
        $client
            ->setUser($user)
            ->setName($name)
            ->setSurnames($surnames)
            ->setPhoneNumber($phoneNumber)
            ->setAddress($address);

        $this->userRepository->add($user);
        $this->clientRepository->addClient($client);

        return new JsonResponse([
            'message' => 'Client successfully registered',
            'client' => ClientDTO::fromEntity($client)
        ], 201);

    }

}