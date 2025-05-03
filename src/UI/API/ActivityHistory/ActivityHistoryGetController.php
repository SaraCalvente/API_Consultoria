<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByNameAndProjectService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryByNameAndProjectDTO;
use App\Shared\Domain\Exception\ActivityHistoryNotFoundException;
use App\Shared\Domain\Exception\ProjectNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ActivityHistoryGetController extends AbstractController
{
    #[Route('/activity', name: 'get_activity_by_name_and_project', methods: ['GET'])]
    #[OA\Get(
        path: "/activity",
        description: "Retrieve activity by name and project name for admin.",
        summary: "Get activity",
        parameters: [
            new OA\Parameter(
                name: "name",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "This is the name of a activity"
            ),
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
                description: "Activity retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "activity_id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "This is the name of a activity"),
                        new OA\Property(property: "description", type: "string", example: "This is a activity description."),
                        new OA\Property(property: "project_id", type: "integer", example: 2),
                        new OA\Property(property: "date", type: "string", example: "2025-03-20"),
                        new OA\Property(property: "consultantId", type: "integer", example: 1),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Not Found")
        ]
    )]
    public function getActivityByNameAndProject(Request $request, ActivityHistoryGetByNameAndProjectService $findByNameAndProjectService): JsonResponse
    {
        try {
            $dto = new ActivityHistoryByNameAndProjectDTO(
                projectName: (string) $request->query->get('projectName'),
                name: (string) $request->query->get('name')
            );

            $activity = $findByNameAndProjectService($dto);

            return new JsonResponse([
                'message' => 'Activity retrieved successfully',
                'task' => $activity
            ], 200);

        } catch (ProjectNotFoundException|ActivityHistoryNotFoundException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
        }
    }
}
