<?php

namespace App\Consultant\Application\Consultant\Admin;

use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantUpdateByEmailService
{
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;
    private AbilityRepositoryInterface $abilityRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface       $userRepository,
        AbilityRepositoryInterface     $abilityRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
        $this->abilityRepository = $abilityRepository;
    }

    public function __invoke( string $email, ?string $profile = null, ?array $addAbilities = null, ?array $removeAbilities = null
    ): JsonResponse {
        $user = $this->userRepository->findUserByEmail($email);
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if ($addAbilities !== null) {
            $this->updateConsultantAbilities($consultant, $addAbilities, true);
        }

        if ($removeAbilities !== null) {
            $this->updateConsultantAbilities($consultant, $removeAbilities, false);
        }

        if ($profile !== null) {
            $consultant->setProfile(Profile::from($profile));
        }

        $this->consultantRepository->saveConsultant();

        return new JsonResponse(ConsultantDTO::fromEntity($consultant));
    }

    private function updateConsultantAbilities(Consultant $consultant, array $abilities, bool $add): void
    {
        foreach ($abilities as $ability) {
            $abilityFind = $this->abilityRepository->findAbilityByNameAndLevel($ability['name'], $ability['level']);

            if ($add) {
                $consultant->addAbility($abilityFind);

            } else {
                $consultant->removeAbility($abilityFind);

            }
        }
    }
}