<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Availability;

use App\Consultant\Application\Ability\AbilityGetByConsultantService;
use App\Consultant\Application\Availability\AvailabilityGetByConsultantEmailService;
use App\Consultant\Application\Availability\AvailabilityGetByConsultantService;
use App\Consultant\Domain\Availability\AvailabilityDTO;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AvailabilityGetByConsultantEmailController extends AbstractController
{
    #[Route('/admin/availability/consultant', name: 'get_consultant_availability', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/availability/consultant",
        description: "Retrieve the availability for a consultant by an authenticated admin.",
        summary: "Get availability",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                ]
            )
        ),
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
    public function getAvailability(Request $request, AvailabilityGetByConsultantEmailService $availabilityGetByConsultantEmailService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $availabilityGetByConsultantEmailService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }


}