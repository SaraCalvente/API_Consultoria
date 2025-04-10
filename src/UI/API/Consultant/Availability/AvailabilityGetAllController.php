<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Availability;

use App\Consultant\Application\Ability\AbilityGetAllService;
use App\Consultant\Application\Availability\AvailabilityGetAllService;
use App\Consultant\Application\Consultant\Admin\ConsultantFindAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AvailabilityGetAllController extends AbstractController
{
    #[Route('/admin/availabilities', name: 'get_all_availabilities', methods: ['GET'])]
    #[OA\Get(
        path: "/availabilities",
        description: "Retrieve all availabilities for an authenticated admin or consultant.",
        summary: "Get all availabilities",
        responses: [
            new OA\Response(
                response: 201,
                description: "Availabilities retrieved successfully.",
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
    public function getAllAvailabilities(AvailabilityGetAllService $availabilityGetAllService): JsonResponse
    {
        return $availabilityGetAllService();
    }


}