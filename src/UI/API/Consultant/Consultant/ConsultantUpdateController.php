<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Consultant;

use App\Consultant\Application\Consultant\ConsultantUpdateByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ConsultantUpdateController extends AbstractController
{

    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @throws \Exception
     */
    #[Route('/consultant/update', name: 'consultant_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/consultant/update",
        description: "Update consultant details of authenticated consultant.",
        summary: "Consultant details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "profile"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "profile", type: "string", example: "Desarrollador"),
                    new Oa\Property(property: "abilityName", type: "string", example: "Habilidad 1"),
                    new Oa\Property(property: "level", type: "string", example: "Medio"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultant updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Consultant updated successfully"),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", example: 1),
                        new OA\Property(property: "email", type: "string", example: "user@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Ana"),
                        new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                        new OA\Property(property: "profile", type: "string", example: "Desarrollador"),
                        new Oa\Property(property: "abilityName", type: "string", example: "Habilidad 1"),
                        new Oa\Property(property: "level", type: "string", example: "Medio"),
                        new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Client not found"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )
        ]
    )]
    public function updateConsultant(Request $request, Security $security, ConsultantUpdateByUserService $consultantUpdateById): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            $data = json_decode($request->getContent(), true);
            return $consultantUpdateById(
                $user,
                $data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}