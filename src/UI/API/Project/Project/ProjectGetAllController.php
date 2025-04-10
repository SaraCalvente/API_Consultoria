<?php
declare(strict_types=1);

namespace App\UI\API\Project\Project;

use App\Project\Application\Project\ProjectFindAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProjectGetAllController extends AbstractController
{
    #[Route('/admin/projects', name: 'get_all_projects', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/projects",
        description: "Retrieve all projects for an authenticated admin.",
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
                                    property: "consultantsIds",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 3),
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
    public function getAllProjects(ProjectFindAllService $projectFindAllService): JsonResponse
    {
        return $projectFindAllService();
    }

}