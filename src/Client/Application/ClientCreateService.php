<?php
declare(strict_types=1);

namespace App\Client\Application;

use App\Client\Domain\Client;
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

class ClientCreateService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private ClientRepositoryInterface   $clientRepository, private UserRepositoryInterface     $userRepository)
    {
    }

    /**
     * @throws NotValidEmailException
     * @throws RequiredFieldException
     */
    public function __invoke(
        array $data
    ): JsonResponse
    {
        $this->validateRequiredFields($data);
        if ($this->userRepository->checkIfUserExists($data['email'])){
            return new JsonResponse([
                'error' => 'User ' . $data['email'] . ' already exists.',

            ], 409);
        }

        $user = new User();
        $user->setEmail(new EmailValueObject($data['email']));
        $this->userRepository->checkPasswordLength($data['password']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CLIENT']);

        $client = new Client();
        $client
            ->setUser($user)
            ->setName($data['name'])
            ->setSurnames($data['surnames'])
            ->setPhoneNumber($data['phoneNumber'])
            ->setAddress($data['address']);

        $this->userRepository->add($user);
        $this->clientRepository->addClient($client);

        return new JsonResponse([
            'message' => 'Client successfully registered',
            'client' => ClientDTO::fromEntity($client)
        ], 201);
    }

    private function validateRequiredFields(array $data): void
    {
        $requiredFields = ['email', 'password', 'name', 'surnames', 'address', 'phoneNumber'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new RequiredFieldException($field);
            }
        }
    }


}