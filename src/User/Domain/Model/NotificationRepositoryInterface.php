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
    public function checkIfNotificationExists(string $email, string $date): bool;

    public function findNotificationByUserAndDate(string $email, string $date): ?Notification;
    public function deleteNotification(Notification $notification): JsonResponse;

    public function addNotification(Notification $notification): void;

    public function saveNotification(): void;
    public function removeNotification(Notification $notification): void;
}