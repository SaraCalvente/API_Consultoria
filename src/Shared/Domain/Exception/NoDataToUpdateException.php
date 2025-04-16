<?php

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoDataToUpdateException extends \Exception
{
    public function __construct()
    {
        parent::__construct("There is no data to update");
    }
}