<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientGetByEmailService;
use App\Client\Domain\ClientByEmailDTO;
use App\Shared\Domain\Exception\ClientNotFoundException;
use App\Shared\Domain\Exception\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ClientGetByEmailController extends AbstractController
{
    #[Route('/admin/client', name: 'admin_get_client', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/client",
        description: "Retrieve client data for an authenticated admin.",
        summary: "Get client",
        parameters: [
            new OA\Parameter(
                name: "email",
                description: "Email of the client to retrieve",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "user@example.com"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Client retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", example: 1),
                        new OA\Property(property: "email", type: "string", example: "user@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Ana"),
                        new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                        new OA\Property(property: "address", type: "string", example: "C/ example, 9"),
                        new OA\Property(property: "phone_number", type: "string", example: "666 666 666"),
                        new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Client not found")
        ]
    )]
    public function getClientAdmin(Request $request, ClientGetByEmailService $clientFindService): JsonResponse
    {
        $email = $request->query->get('email');

        if (!$email) {
            return new JsonResponse(['error' => 'Missing email parameter'], 400);
        }

        try {
            $dto = new ClientByEmailDTO($email);
            $clientDTO = $clientFindService($dto);

            return new JsonResponse($clientDTO, 200);
        }
        catch (UserNotFoundException) {
            return new JsonResponse(['error' => 'No user found'], 404);
        }
        catch (ClientNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }
}
