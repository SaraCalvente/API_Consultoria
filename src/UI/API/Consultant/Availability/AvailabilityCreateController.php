<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Availability;

use App\Consultant\Application\Ability\AbilityCreateService;
use App\Consultant\Application\Availability\AvailabilityCreateService;
use App\Consultant\Application\Consultant\ConsultantCreateService;
use App\Shared\Domain\Auth\AuthChecker;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AvailabilityCreateController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }
    /**
     * @throws \DateMalformedStringException
     */
    #[Route('/availability/create', name: 'availability_create', methods: ['POST'])]
    #[OA\Post(
        path: "/availability/create",
        description: "Create a new availability.",
        summary: "Availability creation.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["available", "startDate", "endDate", "consultantEmail"],
                properties: [
                    new OA\Property(property: "available", type: "bool", example: true),
                    new OA\Property(property: "startDate", type: "string", example: "2025-03-20 08:00:00"),
                    new OA\Property(property: "endDate", type: "string", example: "2025-03-23 08:00:00"),
                    new OA\Property(property: "consultantEmail", type: "string", example: "consultant@gmail.com"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Availability created successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Availability created successfully"),
                        new OA\Property(property: "available", type: "bool", example: true),
                        new OA\Property(property: "startDate", type: "string", example: "2025-03-20 08:00:00"),
                        new OA\Property(property: "endDate", type: "string", example: "2025-03-23 08:00:00"),
                        new OA\Property(property: "consultantId", type: "integer", example: 1),
                    ]
                )
            ),
            new OA\Response(
                response: 402,
                description: "Availability already exists"
            )
        ]
    )]
    public function createAvailability(Security $security,
        Request $request, AvailabilityCreateService $availabilityCreateService
    ): JsonResponse {
        try{
            $user = $this->authChecker->getAuthenticated($security);
            $data = json_decode($request->getContent(), true);
            return $availabilityCreateService($user, $data);
        }catch (ConsultantNotFoundException){
            return new JsonResponse(['error' => 'The authenticated user is not a Consultant.']);
        }
    }



}