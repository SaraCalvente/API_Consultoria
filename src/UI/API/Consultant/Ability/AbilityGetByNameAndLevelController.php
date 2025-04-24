<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Ability;

use App\Consultant\Application\Ability\AbilityGetService;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AbilityGetByNameAndLevelController extends AbstractController
{
    #[Route('/ability', name: 'get_ability', methods: ['GET'])]
    #[OA\Get(
        path: "/ability",
        description: "Retrieve an ability for an authenticated admin or consultant.",
        summary: "Get an ability",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "lavel"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Habilidad 1"),
                    new OA\Property(property: "level", type: "string", example: "Alto")
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Ability retrieved successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ability retrieved successfully."),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Habilidad 1"),
                        new OA\Property(property: "level", type: "string", example: "Alto"),
                    ]
                )
            ),
            new OA\Response(
                response: 402,
                description: "Unauthorized.",
            )
        ]
    )]
    public function getAbility(Request $request, AbilityGetService $abilityGetService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        return $abilityGetService( $data );
    }


}