<?php
declare(strict_types=1);

namespace App\Consultant\Application\Ability;

use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityGetAllService
{
    public function __construct(private AbilityRepositoryInterface $abilityRepository)
    {
    }

    public function __invoke(): JsonResponse
    {
        $abilities = $this->abilityRepository->findAllAbilities();
        $abilitiesData = [];
        foreach ($abilities as $ability) {
            $abilitiesData[] = AbilityDTO::fromEntity($ability);
        }
        if ($abilitiesData === []) {
            return new JsonResponse(['error' => 'There are no activities'], 404);
        }

        return new JsonResponse($abilitiesData, 200);
    }
}