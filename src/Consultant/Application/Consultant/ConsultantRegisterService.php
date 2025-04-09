<?php

namespace App\Consultant\Application\Consultant;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
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
    private AbilityRepositoryInterface $abilityRepository;



    public function __construct(
        UserPasswordHasherInterface   $passwordHasher,
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface       $userRepository,
        AbilityRepositoryInterface $abilityRepository
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
        $this->abilityRepository = $abilityRepository;
    }

    public function __invoke(string $email, string $password, string $name, string $surnames, string $profile, ?array $abilities): JsonResponse
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
        $user->setRoles(['ROLE_CONSULTANT']);
        $this->userRepository->add($user);

        $consultant = new Consultant();
        $consultant->setName($name);
        $consultant->setSurnames($surnames);
        $consultant->setProfile(Profile::from($profile));
        $consultant->setUser($user);
        if ($abilities !== null) {
            foreach ($abilities as $ability) {
                if($this->abilityRepository->findAbilityByNameAndLevel($ability['abilityName'], $ability['level'])){
                    $ability = new Ability();
                    $ability->setName($ability['abilityName']);
                    $ability->setLevel(Level::from($ability['level']));
                    $consultant->addAbility($ability);
                }
            }
        }

        $this->consultantRepository->addConsultant($consultant);

        return new JsonResponse([
            'message' => 'Consultant successfully registered',
            'consultant' => ConsultantDTO::fromEntity($consultant)
            ], 201);
    }
}