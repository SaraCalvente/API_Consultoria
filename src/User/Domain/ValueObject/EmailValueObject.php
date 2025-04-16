<?php

namespace App\User\Domain\ValueObject;

use App\Shared\Domain\Exception\NotValidEmailException;
use App\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

class EmailValueObject extends StringValueObject
{
    /**
     * @throws NotValidEmailException
     */
    public function __construct(string $value)
    {
        $this->isValidEmail($value);
        parent::__construct($value);
    }

    private function isValidEmail(string $value): void
    {
        if (!str_contains($value, '@') || !str_contains($value, '.')) {
            throw new NotValidEmailException($value);
        }
    }
}
