<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Exception;

class RequiredFieldException extends Exception
{
    public function __construct(string $data)
    {
        parent::__construct("The field $data is required.", 400);
    }
}