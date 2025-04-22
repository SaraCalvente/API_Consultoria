<?php
declare(strict_types=1);

namespace App\UI\API\Project\Project;

use App\Project\Application\Project\ProjectCreateService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectCreateController extends AbstractController
{
    #[Route('/project/create', name: 'project_create', methods: ['POST'])]
    #[OA\Post(
        path: "/project/create",
        description: "Create a new project.",
        summary: "Project creation.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "description", "startDate", "status", "consultantsEmails"],
                properties: [
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
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Project registered successfully.",
                content: new OA\JsonContent(
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
                    ]
                )
            ),
            new OA\Response(
                response: 402,
                description: "Project already exists"
            )
        ]
    )]
    public function createProject(
        Request $request, ProjectCreateService $projectCreateService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['name', 'description', 'startDate', 'status', 'clientEmail', 'consultantsEmails'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }

            $endDate = $data['endDate'] ?? null;

            return $projectCreateService(
                $data);

        } catch (\Exception $e){
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }


}