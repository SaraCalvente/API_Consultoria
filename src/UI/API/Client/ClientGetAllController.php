<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientGetAllService;
use App\Shared\Domain\Exception\NoClientsFoundException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ClientGetAllController extends AbstractController
{
    #[Route('/admin/clients', name: 'get_all_clients', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/clients",
        description: "Retrieve all clients for an authenticated admin.",
        summary: "Get all clients",
        responses: [
            new OA\Response(
                response: 200,
                description: "Clients retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "clients", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "user_id", type: "integer", example: 1),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "name", type: "string", example: "Ana"),
                                new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                                new OA\Property(property: "address", type: "string", example: "C/ example, 9"),
                                new OA\Property(property: "phone_number", type: "string", example: "666 666 666"),
                                new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string")),
                            ],
                            type: "object"
                        ))
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 404, description: "No clients found")
        ]
    )]
    public function getAllClients(ClientGetAllService $clientGetAllService): JsonResponse
    {
        try {
            $clients = $clientGetAllService();
            return new JsonResponse(['clients' => $clients], 200);
        } catch (NoClientsFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }
}