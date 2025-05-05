<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Client;
use App\Client\Domain\ClientCreateDTO;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\Shared\Domain\Exception\NotValidPasswordLengthException;
use App\Shared\Domain\Exception\RequiredFieldException;
use App\Shared\Domain\Exception\UserAlreadyExistsException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use MiniOrange\Helper\Exception\RequiredFieldsException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class ClientCreateService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private ClientRepositoryInterface $clientRepository,
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * @throws NotValidEmailException
     * @throws RequiredFieldException
     * @throws NotValidPasswordLengthException
     * @throws UserAlreadyExistsException
     */
    public function __invoke(ClientCreateDTO $dto): ClientDTO
    {
        if ($this->userRepository->checkIfUserExists($dto->email)) {
            throw new UserAlreadyExistsException('User with email ' . $dto->email . ' already exists.');
        }

        $user = new User();
        $user->setEmail(new EmailValueObject($dto->email));
        $this->userRepository->checkPasswordLength($dto->password);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CLIENT']);

        $client = new Client();
        $client
            ->setUser($user)
            ->setName($dto->name)
            ->setSurnames($dto->surnames)
            ->setPhoneNumber($dto->phoneNumber)
            ->setAddress($dto->address);

        $this->userRepository->add($user);
        $this->clientRepository->addClient($client);

        return ClientDTO::fromEntity($client);
    }



}