<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Ability;

use App\Consultant\Application\Ability\AbilityGetAllService;
use App\Consultant\Application\Consultant\Admin\ConsultantGetAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AbilityGetAllController extends AbstractController
{
    #[Route('/abilities', name: 'get_all_abilities', methods: ['GET'])]
    #[OA\Get(
        path: "/abilities",
        description: "Retrieve all abilities for an authenticated admin or consultant.",
        summary: "Get all abilities",
        responses: [
            new OA\Response(
                response: 201,
                description: "Abilities retrieved successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Abilities retrieved successfully"),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Habilidad 1"),
                        new OA\Property(property: "level", type: "string", example: "Alto"),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "No authorized user.",
            )
        ]
    )]
    public function getAllAbilities(AbilityGetAllService $abilityGetAllService): JsonResponse
    {
        return $abilityGetAllService();
    }


}