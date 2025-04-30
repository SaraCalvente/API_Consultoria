<?php

namespace App\Consultant\Application\Consultant;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ConsultantCreateService
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

    /**
     * @throws NotValidEmailException
     */
    public function __invoke(array $data): JsonResponse
    {
        if ($this->userRepository->checkIfUserExists($data['email'])) {
            $user = $this->userRepository->findUserByEmail($data['email']);
            return new JsonResponse([
                'error' => 'User ' . $user->getEmail() . ' already exists as ' . implode(', ', $user->getRoles()),

            ], 403);
        }

        $user = new User();
        $user->setEmail(new EmailValueObject($data['email']));
        $this->userRepository->checkPasswordLength($data['password']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_CONSULTANT']);
        $this->userRepository->add($user);

        $consultant = new Consultant();
        $consultant->setName($data['name']);
        $consultant->setSurnames($data['surnames']);
        $consultant->setProfile(Profile::from($data['profile']));
        $consultant->setUser($user);
        if ($data['abilities'] !== null) {
            foreach ($data['abilities'] as $ability) {
                if(!$this->abilityRepository->findAbilityByNameAndLevel($ability['abilityName'], $ability['level'])){
                    $newAbility = new Ability();
                    $newAbility->setName($ability['abilityName']);
                    $newAbility->setLevel(Level::from($ability['level']));
                } else{
                    $newAbility = $this->abilityRepository->findAbilityByNameAndLevel($ability['abilityName'], $ability['level']);
                }
                $consultant->addAbility($newAbility);
            }
        }

        $this->consultantRepository->addConsultant($consultant);

        return new JsonResponse([
            'message' => 'Consultant successfully registered',
            'consultant' => ConsultantDTO::fromEntity($consultant)
            ], 201);
    }
}