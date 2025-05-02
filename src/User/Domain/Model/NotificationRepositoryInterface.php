<?php

namespace App\User\Domain\Model;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Consultant\Consultant;
use App\User\Domain\Notification;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

interface NotificationRepositoryInterface
{
    public function findAllNotifications(): array;

    public function findNotificationsByCreatorUser(User $user): array;
    public function findNotificationsByUser(User $user): array;
    public function checkIfNotificationExists(User $user, string $date): bool;

    public function findNotificationByUserAndDate(User $user, \DateTime $date): ?Notification;
    public function findReceivedNotificationsByUserAndDate(User $user, \DateTime $date): array;
    public function findCreatedNotificationsByUserAndDate(User $user, \DateTime $date): array;
    public function addNotification(Notification $notification): void;

    public function findNotificationById(int $id): Notification;
    public function removeNotification(Notification $notification): void;
}