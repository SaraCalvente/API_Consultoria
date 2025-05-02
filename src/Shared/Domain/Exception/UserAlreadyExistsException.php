<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAlreadyExistsException extends Exception
{
    public function __construct(string $email)
    {
        parent::__construct("User $email already exists", 409);
    }
}