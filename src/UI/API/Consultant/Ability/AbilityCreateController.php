<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Ability;

use App\Consultant\Application\Ability\AbilityCreateService;
use App\Consultant\Application\Consultant\ConsultantCreateService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AbilityCreateController extends AbstractController
{

    #[Route('/ability/create', name: 'ability_create', methods: ['POST'])]
    #[OA\Post(
        path: "/ability/create",
        description: "Create a new ability.",
        summary: "Ability creation.",
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
                description: "Ability created successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ability created successfully"),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Habilidad 1"),
                        new OA\Property(property: "level", type: "string", example: "Alto"),
                    ]
                )
            ),
            new OA\Response(
                response: 402,
                description: "Ability already exists"
            )
        ]
    )]
    public function createAbility(
        Request $request, AbilityCreateService $abilityCreateService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        return $abilityCreateService( $data );
    }



}