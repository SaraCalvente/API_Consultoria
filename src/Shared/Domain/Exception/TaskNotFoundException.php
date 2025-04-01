<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Task not found');
    }
}