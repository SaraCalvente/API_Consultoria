<?php
declare(strict_types=1);

namespace App\UI\API\Project\Task;

use App\Project\Application\Task\TaskUpdateByNameAndProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class TaskUpdateController extends AbstractController
{
    #[Route('/task/update', name: 'task_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/task/update",
        description: "Update task details by name.",
        summary: "Task details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "projectName"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Task name"),
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                    new OA\Property(property: "description", type: "string", example: "New description"),
                    new OA\Property(property: "status", type: "string", example: "Pendiente"),
                    new OA\Property(property: "endDate", type: "string", example: "2025-03-20 08:00:00"),
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
                description: "Task updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "task_id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "This is the name of a task"),
                        new OA\Property(property: "description", type: "string", example: "This is a task description."),
                        new OA\Property(property: "project_id", type: "integer", example: 2),
                        new OA\Property(property: "startDate", type: "string", example: "2025-03-20 08:00:00"),
                        new OA\Property(property: "endDate", type: "string", example: "2025-03-20 08:00:00"),
                        new OA\Property(property: "status", type: "string", example: "Pendiente"),
                        new OA\Property(
                            property: "consultantsIds",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "int", example: 3),
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
    public function updateProject(Request $request, TaskUpdateByNameAndProjectService $tareaUpdateByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $tareaUpdateByNameService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}