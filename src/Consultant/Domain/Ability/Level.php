<?php

namespace App\Consultant\Domain\Ability;

enum Level: string
{
    case HIGH = 'Alto';
    case LOW = 'Bajo';
    case EXPERT = 'Experto';
    case MEDIUM = 'Medio';
}