<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoClientsFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('Clients not found');
    }
}