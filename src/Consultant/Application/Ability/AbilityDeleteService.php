<?php
declare(strict_types=1);

namespace App\Consultant\Application\Ability;

use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityDeleteService
{
    public function __construct(private AbilityRepositoryInterface $abilityRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        if (!$this->abilityRepository->checkIfAbilityExists($data['name'], $data['level'])) {
            return new JsonResponse(['error' => 'An ability with the name ' . $data['name'] . ' does not exists.' ], 403);
        }
        $ability = $this->abilityRepository->findAbilityByNameAndLevel($data['name'], $data['level']);
        $this->abilityRepository->deleteAbility($ability);


        return new JsonResponse([
            'message' => 'Ability successfully deleted.',
        ], 201);
    }
}