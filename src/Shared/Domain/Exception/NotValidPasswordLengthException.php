<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Exception;

class NotValidPasswordLengthException extends Exception
{
    public function __construct()
    {
        parent::__construct("The password needs to have 5 or more characters", 400);
    }
}