<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientGetByUserService;
use App\Client\Domain\Client;
use App\Shared\Domain\Auth\AuthChecker;
use App\Shared\Domain\Exception\ClientNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class ClientGetController extends AbstractController
{
    public function __construct(private AuthChecker $authChecker)
    {
    }
    #[Route('/client', name: 'get_client', methods: ['GET'])]

    #[OA\Get(
        path: "/client",
        description: "Retrieve client data for an authenticated client.",
        summary: "Get client",
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
                        new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT"),

                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )]
    )]

    public function getClient(Security $security, ClientGetByUserService $clientFindService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $clientFindService($user);
        } catch (ClientNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }
}