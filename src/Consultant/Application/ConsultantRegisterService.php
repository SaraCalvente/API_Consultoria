<?php

namespace App\Consultant\Application;

use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Consultant\Domain\Profile;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConsultantRegisterService
{
    private UserPasswordHasherInterface $passwordHasher;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        UserPasswordHasherInterface   $passwordHasher,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface       $userRepository
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $email, string $password, string $name, string $surnames, string $profile): JsonResponse
    {
        if ($this->userRepository->checkIfUserExists($email)){
            $user = $this->userRepository->findUserByEmail($email);
            return new JsonResponse([
                'error' => 'User ' . $user->getEmail() . ' exists as ' . implode(', ', $user->getRoles()),

            ], 400);
        }

        $user = new User();
        $user->setEmail(new EmailValueObject($email));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CONSULTANT']);
        $this->userRepository->add($user);

        $consultant = new Consultant();
        $consultant->setName($name);
        $consultant->setSurnames($surnames);
        $consultant->setProfile(Profile::from($profile));
        $consultant->setUser($user);
        $this->consultantRepository->addConsultant($consultant);

        return new JsonResponse([
            'message' => 'Consultor registrado correctamente',
            'consultant' => ConsultantDTO::fromEntity($consultant)
            ], 201);
    }
}