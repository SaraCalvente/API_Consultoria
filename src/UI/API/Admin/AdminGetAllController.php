<?php
declare(strict_types=1);

namespace App\UI\API\Admin;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use App\User\Application\AdminFindAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class AdminGetAllController extends AbstractController
{
    #[Route('/admins', name: 'get_admin_users', methods: ['GET'])]

    #[OA\Get(
        path: "/admins",
        description: "Retrieve all the admins.",
        summary: "Get a list of all admins.",
        responses: [
            new OA\Response(
                response: 200,
                description: "Admins retrieved successfully.",
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
            new OA\Response(
                response: 401,
                description: "Not authorized.",
            ),
        ]
    )]

    #[Security(name: "Bearer")]
    public function getAdminUsers(AdminFindAllService $adminFindAllService): JsonResponse
    {
        return $adminFindAllService();
    }

}