<?php

namespace App\Consultant\Domain\Model;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Consultant\Consultant;
use Symfony\Component\HttpFoundation\JsonResponse;

interface AbilityRepositoryInterface
{
    public function findAllAbilities(): array;

    public function findAbilitiesByConsultant(Consultant $consultant): array;

    public function checkIfAbilityExists(string $name, string $level): bool;

    public function findAbilityByNameAndLevel(string $name, string $level): ?Ability;

    public function updateAbility(Ability $ability, ?string $name, ?string $level): JsonResponse;

    public function deleteAbility(Ability $ability): JsonResponse;

    public function addAbility(Ability $ability): void;

    public function saveAbility(): void;
    public function removeAbility(Ability $ability): void;
}