<?php
declare(strict_types=1);

namespace App\Consultant\Application\Ability;

use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityGetAllService
{
    private AbilityRepositoryInterface $abilityRepository;


    public function __construct(
        AbilityRepositoryInterface $abilityRepository
    )
    {
        $this->abilityRepository = $abilityRepository;
    }

    public function __invoke(): JsonResponse
    {
        $abilities = $this->abilityRepository->findAllAbilities();
        $abilitiesData = [];
        foreach ($abilities as $ability) {
            $abilitiesData[] = AbilityDTO::fromEntity($ability);
        }

        return new JsonResponse($abilitiesData, 200);
    }
}