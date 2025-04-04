<?php

namespace App\UI\API\Admin;
use OpenApi\Attributes as OA;
use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\AdminDeleteByIdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class AdminDeleteUserController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @throws \Exception
     */
    #[Route('/admin/delete', name: 'delete_admin', methods: ['DELETE'])]

    #[OA\Delete(
        path: "/admin/delete",
        description: "Deletes the authenticated admin",
        summary: "Admin deleted successfully",
        responses: [
            new OA\Response(
                response: 200,
                description: "Admin deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Admin deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Not authorized"
            ),
        ]
    )]
    public function deleteAdmin( Security $security, AdminDeleteByIdService $adminDeleteByIdService): JsonResponse
    {
        $user = $this->authChecker->getAuthenticated($security);
        return $adminDeleteByIdService($user);

    }
}