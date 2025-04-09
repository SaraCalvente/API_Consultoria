<?php

namespace App\Consultant\Domain\Ability;

class AbilityDTO
{
    public static function fromEntity(Ability $ability): array
    {
        return [
            'ability_id' => $ability->getId(),
            'name' => $ability->getName(),
            'level' => $ability->getLevel(),
        ];
    }
}