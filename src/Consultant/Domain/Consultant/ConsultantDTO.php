<?php

namespace App\Consultant\Domain\Consultant;

class ConsultantDTO
{
    public static function fromEntity(Consultant $consultant): array
    {

        return [
            'user_id' => $consultant->getUser()->getId(),
            'consultant_id' => $consultant->getId(),
            'email' => $consultant->getUser()->getEmail(),
            'name' => $consultant->getName(),
            'profile' => $consultant->getProfile(),
            'abilities' => array_map(fn($c) => ['name' => $c->getName(), 'level' => $c->getLevel()->value], $consultant->getAbilities()->toArray()),
            'roles' => $consultant->getUser()->getRoles()
        ];
    }
}