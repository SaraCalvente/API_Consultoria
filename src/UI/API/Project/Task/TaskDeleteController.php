<?php

namespace App\UI\API\Project\Task;

use App\Project\Application\Task\TaskDeleteByNameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class TaskDeleteController extends AbstractController
{
    #[Route('/task/delete', name: 'delete_task', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/task/delete",
        description: "Deletes the authenticated task.",
        summary: "Task deleted successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "projectName"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "This is the name of a task"),
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Task deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Task deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Not authorized"
            ),
        ]
    )]
    public function deleteTask(Request $request, TaskDeleteByNameService $taskDeleteByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $taskDeleteByNameService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}