<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\Admin\ClientUpdateByEmailService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;


class ClientAdminUpdateController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/admin/client/update', name: 'admin_client_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/admin/client/update",
        description: "Updates client details by an authenticated admin.",
        summary: "Client details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "address", "phone_number"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "address", type: "string", example: "C/ example, 9"),
                    new OA\Property(property: "phone_number", type: "string", example: "666 666 666")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Client updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Client updated successfully"),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", example: 1),
                        new OA\Property(property: "email", type: "string", example: "user@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Ana"),
                        new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                        new OA\Property(property: "address", type: "string", example: "C/ example, 9"),
                        new OA\Property(property: "phone_number", type: "string", example: "666 666 666"),
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
    public function adminUpdateClient(Request $request, ClientUpdateByEmailService $clientUpdateByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $clientUpdateByEmail(
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['phoneNumber'] ?? null
        );
    }


}