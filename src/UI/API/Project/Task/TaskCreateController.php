<?php
declare(strict_types=1);

namespace App\UI\API\Project\Task;

use App\Project\Application\Task\TaskCreateSevice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class TaskCreateController extends AbstractController
{
    #[Route('/task/create', name: 'task_create', methods: ['POST'])]
    #[OA\Post(
        path: "/task/create",
        description: "Create a new task.",
        summary: "Task creation.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["projectName", "name", "description", "startDate", "endDate", "status"],
                properties: [
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                    new OA\Property(property: "name", type: "string", example: "This is the name of a task"),
                    new OA\Property(property: "description", type: "string", example: "This is a task description."),
                    new OA\Property(property: "startDate", type: "string", example: "2025-03-20 08:00:00"),
                    new OA\Property(property: "endDate", type: "string", example: "2025-03-20 08:00:00"),
                    new OA\Property(property: "status", type: "string", example: "Pendiente"),
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
                description: "Task created successfully.",
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
                response: 402,
                description: "Task already exists"
            )
        ]
    )]
    public function createTask(
        Request $request, TaskCreateSevice $taskCreateService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['projectName', 'name', 'description', 'startDate', 'status', 'consultantsEmails'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }

            return $taskCreateService(
                $data['projectName'], $data['name'],
                $data['description'], $data['startDate'], $data['endDate'],
                $data['status'], $data['consultantsEmails']);

        } catch (\Exception $e){
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }

}