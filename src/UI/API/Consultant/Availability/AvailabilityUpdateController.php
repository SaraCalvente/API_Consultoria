<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Availability;

use App\Consultant\Application\Ability\AbilityUpdateService;
use App\Consultant\Application\Availability\AvailabilityUpdateService;
use App\Consultant\Application\Consultant\ConsultantUpdateByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AvailabilityUpdateController extends AbstractController
{

    /**
     * @throws \Exception
     */
    #[Route('/availability/update', name: 'availability_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/availability/update",
        description: "Update availability details.",
        summary: "Availability details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "startDate"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "consultant@email.com"),
                    new OA\Property(property: "startDate", type: "string", example: "2025-04-19 10:00:00"),
                    new Oa\Property(property: "endDate", type: "string", example: "2025-04-20 20:00:00"),
                    new Oa\Property(property: "available", type: "bool", example: false),
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
    public function updateConsultant(Request $request, AvailabilityUpdateService $availabilityUpdateService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $availabilityUpdateService(
                $data['email'],
                $data['startDate'],
                $data['endDate'] ?? null,
                $data['available'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}