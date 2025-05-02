<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Availability;

use App\Consultant\Application\Ability\AbilityGetService;
use App\Consultant\Application\Availability\AvailabilityGetByConsultantAndStartDateService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class AvailabilityGetByConsultantAndStartDateController extends AbstractController
{
    public function __construct(private AuthChecker $authChecker)
    {
    }
    #[Route('/availability', name: 'get_availability', methods: ['GET'])]
    #[OA\Get(
        path: "/availability",
        description: "Retrieve an availability for an authenticated admin or consultant.",
        summary: "Get an availability",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "startDate"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "consultant@example.com"),
                    new OA\Property(property: "startDate", type: "string", example: "2025-04-19 10:00:00")
                ],
                type: "object"
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
    public function getAbility(Request $request, Security $security,  AvailabilityGetByConsultantAndStartDateService $getByConsultantAndStartDateService): JsonResponse
    {
        $user = $this->authChecker->getAuthenticated($security);
        $email = $request->query->get('email') ?? null;
        $startDate = $request->query->get('startDate');

        $data = [
            'email' => $email,
            'startDate' => $startDate
        ];

        return $getByConsultantAndStartDateService($user, $data);
    }


}