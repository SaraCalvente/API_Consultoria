<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Project not found');
    }
}