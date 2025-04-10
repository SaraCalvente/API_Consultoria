<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Availability;

use App\Consultant\Application\Ability\AbilityGetByConsultantService;
use App\Consultant\Application\Availability\AvailabilityGetByConsultantService;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AvailabilityGetByConsultantController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }
    #[Route('/consultant/availability', name: 'get_consultant_availability', methods: ['GET'])]
    #[OA\Get(
        path: "consultant/availability",
        description: "Retrieve the availability for an authenticated consultant.",
        summary: "Get availability",
        responses: [
            new OA\Response(
                response: 201,
                description: "Availability retrieved successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Availabilities retrieved successfully"),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "available", type: "bool", example: true),
                        new OA\Property(property: "start_date", type: "string", example: "2025-04-19 10:00:00"),
                        new OA\Property(property: "end_date", type: "string", example: "2025-04-19 10:00:00"),
                        new OA\Property(property: "consultant_id", type: "integer", example: 1),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No authorized user.",
            )
        ]
    )]
    public function getAvailability(Security $security, AvailabilityGetByConsultantService $availabilityGetByConsultantService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $availabilityGetByConsultantService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }


}