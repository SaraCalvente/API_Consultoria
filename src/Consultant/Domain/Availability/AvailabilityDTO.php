<?php

namespace App\Consultant\Domain\Availability;

class AvailabilityDTO
{
    public static function fromEntity(Availability $availability): array
    {
        return [
            'availability_id' => $availability->getId(),
            'available' => $availability->isAvailable(),
            'start_date' => $availability->getStartDate()->format('Y-m-d H:i:s'),
            'end_date' => $availability->getEndDate()->format('Y-m-d H:i:s'),
            'consultant_id' => $availability->getConsultant()->getId(),
        ];
    }
}