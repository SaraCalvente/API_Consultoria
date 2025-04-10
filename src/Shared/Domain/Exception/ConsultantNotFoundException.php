<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConsultantNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Consultant not found');
    }
}