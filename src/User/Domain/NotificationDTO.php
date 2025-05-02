<?php
declare(strict_types=1);

namespace App\User\Domain;

class NotificationDTO
{
    public static function fromEntity(Notification $notification): array
    {
        return [
            'notification_id' => $notification->getId(),
            'creator' => $notification->getCreatorUser()->getEmail(),
            'message' => $notification->getMessage(),
            'date' => $notification->getDate()->format('Y-m-d H:i:s'),
            'receptors_emails' => array_map(fn($c) => $c->getEmail(), $notification->getUser()->toArray())
        ];
    }
}