<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Availability;

use App\Consultant\Application\Ability\AbilityDeleteService;
use App\Consultant\Application\Availability\AvailabilityDeleteService;
use App\Consultant\Application\Consultant\ConsultantDeleteByIdService;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AvailabilityDeleteController extends AbstractController
{
    #[Route('/availability/delete', name: 'delete_availability', methods: ['DELETE'])]
    #[OA\Put(
        path: "/availability/delete",
        description: "Delete availability details.",
        summary: "Availability deleted successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "startDate"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "consultant@email.com"),
                    new OA\Property(property: "startDate", type: "string", example: "2025-04-19 10:00:00")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Availability deleted successfully.",

            ),
            new OA\Response(
                response: 401,
                description: "No authorized user.",
            )
        ]
    )]
    public function deleteAvailability(Request $request, AvailabilityDeleteService $availabilityDeleteService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $availabilityDeleteService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}