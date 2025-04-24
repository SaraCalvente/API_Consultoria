<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Ability;

use App\Consultant\Application\Ability\AbilityDeleteService;
use App\Consultant\Application\Consultant\ConsultantDeleteByIdService;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AbilityDeleteController extends AbstractController
{
    #[Route('/ability/delete', name: 'delete_ability', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/ability/delete",
        description: "Deletes the authenticated ability.",
        summary: "Ability deleted successfully",
        responses: [
            new OA\Response(
                response: 200,
                description: "Ability deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Ability deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Not authorized"
            ),
        ]
    )]
    public function deleteConsultant(Request $request, AbilityDeleteService $abilityDeleteService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $abilityDeleteService( $data );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}