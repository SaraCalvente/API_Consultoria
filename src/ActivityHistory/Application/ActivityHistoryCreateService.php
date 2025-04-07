<?php
declare(strict_types=1);

namespace App\ActivityHistory\Application;

use App\Client\Domain\Client;
use App\Client\Domain\ClientDTO;
use App\Client\Domain\Model\ClientRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ActivityHistoryCreateService
{

}