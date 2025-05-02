<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotValidEmailException extends Exception
{
    public function __construct(string $email)
    {
        parent::__construct("The email $email is not correct. Please, insert a valid email.", 409);
    }
}