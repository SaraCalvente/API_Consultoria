<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\Admin\ClientGetAllService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ClientGetAllController extends AbstractController
{
    /**
     * @param ClientGetAllService $clientFindAllService
     * @return JsonResponse
     */
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
                                new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT"),

                            ],
                            type: "object"
                        ))
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )]
    )]
    public function getAllClients(ClientGetAllService $clientFindAllService): JsonResponse
    {
        return $clientFindAllService();
    }

}