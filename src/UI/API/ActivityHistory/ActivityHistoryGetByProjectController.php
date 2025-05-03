<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByProjectService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryByProjectDTO;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
class ActivityHistoryGetByProjectController extends AbstractController
{
    #[Route('/activity/project/activities', name: 'get_all_project_activities', methods: ['GET'])]
    #[OA\Get(
        path: "/activity/project/activities",
        description: "Retrieve all activities for a project.",
        summary: "Get all project activities",
        parameters: [
            new OA\Parameter(
                name: "projectName",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "Project 1"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Activities retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "tasks",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "activity_id", type: "integer", example: 1),
                                    new OA\Property(property: "name", type: "string", example: "Design"),
                                    new OA\Property(property: "description", type: "string", example: "Initial wireframes"),
                                    new OA\Property(property: "project_id", type: "integer", example: 2),
                                    new OA\Property(property: "date", type: "string", example: "2025-03-20"),
                                    new OA\Property(property: "user_id", type: "integer", example: 3)
                                ],
                                type: "object"
                            )
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Project not found")
        ]
    )]
    public function getAllProjectTasks(Request $request, ActivityHistoryGetByProjectService $service): JsonResponse
    {
        try {
            $dto = new ActivityHistoryByProjectDTO(
                projectName: (string) $request->query->get('projectName')
            );

            $activities = $service($dto);

            return new JsonResponse([
                'message' => 'Tasks retrieved successfully',
                'tasks' => $activities
            ], 200);

        } catch (ProjectNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }
}