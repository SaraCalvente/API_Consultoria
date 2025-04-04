<?php
declare(strict_types=1);

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectFindByClientService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class ProjectsGetByUserController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/user/projects', name: 'get_user_projects', methods: ['GET'])]
    #[OA\Get(
        path: "/user/projects",
        description: "Retrieve all projects for an authenticated user.",
        summary: "Get all projects",
        responses: [
            new OA\Response(
                response: 200,
                description: "Projects retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "projects", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "project_id", type: "integer", example: 1),
                                new OA\Property(property: "client_id", type: "integer", example: 2),
                                new OA\Property(property: "name", type: "string", example: "Project 1"),
                                new OA\Property(property: "description", type: "string", example: "This is a project description."),
                                new OA\Property(property: "startDate", type: "string", example: "2025-03-20"),
                                new OA\Property(property: "endDate", type: "string", example: "2025-03-20"),
                                new OA\Property(property: "status", type: "string", example: "Pendiente"),
                                new OA\Property(property: "clientEmail", type: "string", example: "client@example.com"),
                                new OA\Property(
                                    property: "consultantsEmails",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "email", type: "string", example: "consultant@example.com"),
                                        ],
                                        type: "object"
                                    )
                                ),
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
    public function getProjectsByUser(Security $security, ProjectFindByClientService $projectFindByUserService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $projectFindByUserService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}