<?php

namespace App\Consultant\Domain;

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
            'roles' => $consultant->getUser()->getRoles()
        ];
    }
}