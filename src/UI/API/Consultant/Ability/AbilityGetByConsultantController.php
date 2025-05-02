<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Ability;

use App\Consultant\Application\Ability\AbilityGetByConsultantService;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AbilityGetByConsultantController extends AbstractController
{
    public function __construct(private AuthChecker $authChecker)
    {
    }
    #[Route('/consultant/abilities', name: 'get_consultant_ability', methods: ['GET'])]
    #[OA\Get(
        path: "consultant/abilities",
        description: "Retrieve the abilities for an authenticated consultant.",
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
    public function getAbility(Security $security, AbilityGetByConsultantService $abilityGetByConsultantService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $abilityGetByConsultantService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }


}