<?php
declare(strict_types=1);

namespace App\Consultant\Application\Availability;

use App\Consultant\Domain\Ability\AbilityDTO;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\AvailabilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AvailabilityGetAllService
{
    public function __construct(private AvailabilityRepositoryInterface $availabilityRepository)
    {
    }

    public function __invoke(): JsonResponse
    {
        $availabilities = $this->availabilityRepository->findAllAvailabilities();
        $availabilitiesData = [];
        foreach ($availabilities as $availability) {
            $availabilitiesData[] = AvailabilityDTO::fromEntity($availability);
        }
        if ($availabilitiesData === []) {
            return new JsonResponse(['error' => 'There are no activities'], 404);
        }


        return new JsonResponse($availabilitiesData, 200);
    }
}