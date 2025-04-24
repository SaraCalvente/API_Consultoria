<?php

namespace App\Consultant\Application\Consultant;

use App\Consultant\Domain\Consultant\Consultant;
use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Consultant\Profile;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantUpdateByUserService
{
    private ConsultantRepositoryInterface $consultantRepository;
    private AbilityRepositoryInterface $abilityRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository,
        AbilityRepositoryInterface $abilityRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
        $this->abilityRepository = $abilityRepository;
    }

    public function __invoke( User $user, array $data
    ): JsonResponse {
        $consultant = $this->consultantRepository->findConsultantByUser($user);

        if ($data['addAbilities'] !== null) {
            $this->updateConsultantAbilities($consultant, $data['addAbilities'], true);
        }

        if ($data['removeAbilities'] !== null) {
            $this->updateConsultantAbilities($consultant, $data['removeAbilities'], false);
        }

        if ($data['profile'] !== null) {
            $consultant->setProfile(Profile::from($data['profile']));
        }

        $this->consultantRepository->saveConsultant();

        return new JsonResponse(ConsultantDTO::fromEntity($consultant)
        );
    }

    private function updateConsultantAbilities(Consultant $consultant, array $abilities, bool $add): void
    {

        foreach ($abilities as $ability) {
            $abilityFind = $this->abilityRepository->findAbilityByNameAndLevel($ability['abilityName'], $ability['level']);

            if ($add) {
                $consultant->addAbility($abilityFind);

            } else {
                 $consultant->removeAbility($abilityFind);

            }
        }
    }
}