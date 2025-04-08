<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityHistoryNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Activity History not found');
    }
}