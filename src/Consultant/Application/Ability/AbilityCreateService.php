<?php
declare(strict_types=1);

namespace App\Consultant\Application\Ability;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityCreateService
{
    private AbilityRepositoryInterface $abilityRepository;


    public function __construct(
        AbilityRepositoryInterface $abilityRepository
    )
    {
        $this->abilityRepository = $abilityRepository;
    }

    public function __invoke(array $data): JsonResponse
    {
        if ($this->abilityRepository->checkIfAbilityExists($data['name'], $data['level'])) {
            return new JsonResponse(['error' => 'An ability with the name ' . $data['name'] . ' already exists.' ], 403);
        }
        $ability = new Ability();
        $ability->setName($data['name']);
        $ability->setLevel(Level::from($data['level']));
        $this->abilityRepository->addAbility($ability);

        return new JsonResponse([
            'message' => 'Consultant successfully registered',
            'ability' => AbilityDTO::fromEntity($ability)
        ], 201);
    }
}