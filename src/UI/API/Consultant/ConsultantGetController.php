<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantFindByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class ConsultantGetController extends AbstractController
{

    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/consultant', name: 'get_consultant', methods: ['GET'])]
    #[OA\Get(
        path: "/consultant",
        description: "Retrieve consultant data for an authenticated consultant.",
        summary: "Get consultant",
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultant retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", example: 1),
                        new OA\Property(property: "email", type: "string", example: "user@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Ana"),
                        new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                        new OA\Property(property: "profile", type: "string", example: "Desarrollador"),
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
    public function getConsultant(Security $security, ConsultantFindByUserService $consultantFindByIdService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $consultantFindByIdService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }


}