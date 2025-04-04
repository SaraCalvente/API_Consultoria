<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\Admin\ConsultantUpdateByEmailService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ConsultantAdminUpdateController extends AbstractController
{
    #[Route('/admin/consultant/update', name: 'admin_consultant_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/admin/consultant/update",
        description: "Updates consultant details by an authenticated admin.",
        summary: "Consultant details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "profile"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "profile", type: "string", example: "Desarrollador"),
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
                        new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Consultant not found"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )
        ]
    )]
    public function adminUpdateConsultant(Request $request, ConsultantUpdateByEmailService $consultantUpdateByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $consultantUpdateByEmail(
            $data['email'] ?? null,
            $data['profile'] ?? null
        );
    }

}