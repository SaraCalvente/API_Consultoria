<?php
declare(strict_types=1);

namespace App\UI\API\Project\Task;

use App\Project\Application\Task\TaskFindByProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class TasksGetByProjectController extends AbstractController
{
    #[Route('/admin/project/tasks', name: 'get_all_project_tasks', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/project/tasks",
        description: "Retrieve all tasks for a project.",
        summary: "Get all project tasks",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["projectName"],
                properties: [
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Tasks retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "tasks", type: "array", items: new OA\Items(
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
    public function getAllProjectTasks(Request $request, TaskFindByProjectService $taskFindByProjectService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $taskFindByProjectService($data);
    }

}