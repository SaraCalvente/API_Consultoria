<?php

namespace App\User\Domain\ValueObject;

use App\Shared\Domain\ValueObject\StringValueObject;
use InvalidArgumentException;

class EmailValueObject extends StringValueObject
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        $this->isValidEmail($value);
        parent::__construct($value);
    }

    /**
     * @throws InvalidArgumentException
     */
    private function isValidEmail(string $value): void
    {
        if (!str_contains($value, '@') || !str_contains($value, '.')) {
            throw new InvalidArgumentException(
                sprintf('The email \'%s\' is not correct. Please, insert a valid email', $value)
            );
        }
    }
}
