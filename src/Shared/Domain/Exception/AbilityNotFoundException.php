<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AbilityNotFoundException  extends NotFoundHttpException
{
    public function __construct(string $abilityName, string $level)
    {
        parent::__construct('Ability ' . $abilityName . ' with level ' . $level . ' not found');
    }
}
