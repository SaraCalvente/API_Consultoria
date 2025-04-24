<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Ability;

use App\Consultant\Application\Ability\AbilityUpdateService;
use App\Consultant\Application\Consultant\ConsultantUpdateByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AbilityUpdateController extends AbstractController
{

    /**
     * @throws \Exception
     */
    #[Route('/ability/update', name: 'ability_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/ability/update",
        description: "Update ability details.",
        summary: "Ability details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "level"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Hablidad 1"),
                    new OA\Property(property: "level", type: "string", example: "Bajo"),
                    new Oa\Property(property: "newName", type: "string", example: "Habilidad 1"),
                    new Oa\Property(property: "newLevel", type: "string", example: "Medio"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Ability updated successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ability updated successfully."),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Habilidad 1"),
                        new OA\Property(property: "level", type: "string", example: "Medio"),
                    ]
                )
            ),
            new OA\Response(
                response: 402,
                description: "Unauthorized.",
            )
        ]
    )]
    public function updateConsultant(Request $request, AbilityUpdateService $abilityUpdateService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $abilityUpdateService( $data );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}