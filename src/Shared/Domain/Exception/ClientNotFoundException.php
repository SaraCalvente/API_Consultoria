<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Client not found');
    }
}