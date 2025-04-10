<?php
declare(strict_types=1);

namespace App\UI\API\Project\Project;

use App\Project\Application\Project\ProjectUpdateByNameService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectUpdateController extends AbstractController
{
    #[Route('/project/update', name: 'project_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/project/update",
        description: "Update project details by name.",
        summary: "Project details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Project 1"),
                    new OA\Property(property: "description", type: "string", example: "Description 1"),
                    new OA\Property(property: "status", type: "string", example: "Pendiente"),
                    new OA\Property(property: "endDate", type: "string", example: "2025-03-20"),
                    new OA\Property(
                        property: "addConsultantsEmails",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "email", type: "string", example: "consultant1@example.com"),
                            ],
                            type: "object"
                        )
                    ),
                    new OA\Property(
                        property: "erraseConsultantsEmails",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "email", type: "string", example: "consultant2@example.com"),
                            ],
                            type: "object"
                        )
                    ),

                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Project updated successfully",
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
                response: 404,
                description: "Client not found"
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )
        ]
    )]
    public function updateProject(Request $request, ProjectUpdateByNameService $projectUpdateByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $projectUpdateByNameService(
                $data['name'],
                $data['description'] ?? null,
                $data['status'] ?? null,
                $data['endDate'] ?? null,
                $data['addConsultantsEmails'] ?? null,
                $data['erraseConsultantEmails'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}