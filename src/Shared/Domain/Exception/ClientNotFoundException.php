<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}