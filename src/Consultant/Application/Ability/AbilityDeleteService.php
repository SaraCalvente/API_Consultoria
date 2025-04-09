<?php
declare(strict_types=1);

namespace App\Consultant\Application\Ability;

use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityDeleteService
{
    private AbilityRepositoryInterface $abilityRepository;


    public function __construct(
        AbilityRepositoryInterface $abilityRepository
    )
    {
        $this->abilityRepository = $abilityRepository;
    }

    public function __invoke(string $name, string $level): JsonResponse
    {
        if (!$this->abilityRepository->checkIfAbilityExists($name, $level)) {
            return new JsonResponse(['error' => 'An ability with the name ' . $name . ' does not exists.' ], 403);
        }
        $ability = $this->abilityRepository->findAbilityByNameAndLevel($name, $level);
        $this->abilityRepository->deleteAbility($ability);


        return new JsonResponse([
            'message' => 'Ability successfully deleted.',
        ], 201);
    }
}