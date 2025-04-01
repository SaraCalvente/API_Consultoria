<?php

namespace App\Consultant\Application;

use App\Client\Domain\ClientDTO;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Consultant\Domain\Profile;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConsultantRegisterService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(string $email, string $password, string $name, string $surnames, string $profile): JsonResponse
    {
        if(!$this->consultantRepository->checkIfUserExists($email)){
            return new JsonResponse([
                'error' => 'El usuario ya existe',

            ], 400);
        }

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
        $this->consultantRepository->add($consultant);

        return new JsonResponse([
            'message' => 'Consultor registrado correctamente',
            'consultant' => ConsultantDTO::fromEntity($consultant)
            ], 201);
    }
}