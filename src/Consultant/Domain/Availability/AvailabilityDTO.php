<?php

namespace App\Consultant\Domain\Availability;

class AvailabilityDTO
{
    public static function fromEntity(Availability $availability): array
    {
        return [
            'availability_id' => $availability->getId(),
            'available' => $availability->isAvailable(),
            'start_date' => $availability->getStartDate(),
            'end_date' => $availability->getEndDate(),
            'consultant_id' => $availability->getConsultant()->getId(),
        ];
    }
}