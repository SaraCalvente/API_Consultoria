<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivityHistoryNotFromUserException extends NotFoundHttpException
{
    public function __construct(string $activityName, string $projectName)
    {
        parent::__construct('Activity History ' . $activityName . ' from project ' . $projectName . ' does not belong to you');
    }
}