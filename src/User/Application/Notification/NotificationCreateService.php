<?php
declare(strict_types=1);

namespace App\User\Application\Notification;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class NotificationCreateService
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
        if ($this->abilityRepository->checkIfAbilityExists($name, $level)) {
            return new JsonResponse(['error' => 'An ability with the name ' . $name . ' already exists.' ], 403);
        }
        $ability = new Ability();
        $ability->setName($name);
        $ability->setLevel(Level::from($level));
        $this->abilityRepository->addAbility($ability);

        return new JsonResponse([
            'message' => 'Consultant successfully registered',
            'ability' => AbilityDTO::fromEntity($ability)
        ], 201);
    }
}