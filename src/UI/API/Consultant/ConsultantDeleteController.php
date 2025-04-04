<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantDeleteByIdService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class ConsultantDeleteController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/consultant/delete', name: 'delete_consultant', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/consultant/delete",
        description: "Deletes the authenticated consultant.",
        summary: "Consultant deleted successfully",
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultant deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Consultant deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Not authorized"
            ),
        ]
    )]
    public function deleteConsultant(Security $security, ConsultantDeleteByIdService $consultantDeleteByIdService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $consultantDeleteByIdService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}