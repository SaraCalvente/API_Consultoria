<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Ability;

use App\Consultant\Application\Ability\AbilityGetByConsultantEmailService;
use App\Consultant\Application\Ability\AbilityGetByConsultantService;
use App\Consultant\Application\Ability\AbilityGetService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AbilityGetByConsultantEmailController extends AbstractController
{
    #[Route('/admin/abilities/consultant', name: 'get_by_email_consultant_ability', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/abilities/consultant",
        description: "Retrieve the abilities for a user by an authenticated admin.",
        summary: "Get abilities",
        responses: [
            new OA\Response(
                response: 201,
                description: "Abilities retrieved successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Abilities retrieved successfully."),
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
    public function getAbility(Request $request, AbilityGetByConsultantEmailService $abilityGetByConsultantEmailService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $abilityGetByConsultantEmailService($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }


}