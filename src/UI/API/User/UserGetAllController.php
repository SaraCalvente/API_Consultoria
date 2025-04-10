<?php
declare(strict_types=1);

namespace App\UI\API\User;

use App\User\Application\UserFindAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class UserGetAllController extends AbstractController
{
    #[Route('/users', name: 'get_all_users', methods: ['GET'])]
    #[OA\Get(
        path: "/users",
        description: "Retrieve all users.",
        summary: "Get a list of all users.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Users retrieved successfully.",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", type: "int", example: "2"),
                            new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                            new OA\Property(property: "roles", type: "string", example: "ROLE_ADMIN")
                        ],
                        type: "object"
                    )
                )
            ),
        ]
    )]
    public function getUsers(UserFindAllService $userFindAllService): JsonResponse
    {
        return $userFindAllService();
    }

}