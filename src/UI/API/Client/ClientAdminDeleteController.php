<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\Admin\ClientDeleteByEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
class ClientAdminDeleteController extends AbstractController
{
    #[Route('/admin/client/delete', name: 'admin_delete_client', methods: ['DELETE'])]

    #[OA\Delete(
        path: "/admin/client/delete",
        description: "Deletes the client by an authenticated admin",
        summary: "Client deleted successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Client deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Client deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Not authorized"
            ),
            new OA\Response(
                response: 402,
                description: "Cannot delete client because there are associated projects"
            )
        ]
    )]
    public function adminDeleteClient (Request $request, ClientDeleteByEmailService $clientDeleteByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            return $clientDeleteByEmail($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}