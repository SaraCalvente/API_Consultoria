<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryDeleteByNameService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryDeleteDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ActivityHistoryDeleteController extends AbstractController
{
    #[Route('/activity/delete', name: 'delete_activity', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/activity/delete",
        description: "Deletes the activity with the given name from a project.",
        summary: "Delete activity by name and project",
        parameters: [
            new OA\Parameter(
                name: "projectName",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "Project 1"
            ),
            new OA\Parameter(
                name: "name",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "Historial 7"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Activity deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Activity deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Activity or project not found"
            ),
            new OA\Response(
                response: 400,
                description: "Invalid request"
            )
        ]
    )]
    public function deleteActivity(
        Request $request,
        ActivityHistoryDeleteByNameService $activityHistoryDeleteByNameService
    ): JsonResponse
    {
        try {
            $projectName = $request->query->get('projectName');
            $name = $request->query->get('name');

            if (!$projectName || !$name) {
                return new JsonResponse(['error' => 'Missing required parameters'], 400);
            }

            $dto = new ActivityHistoryDeleteDTO($projectName, $name);
            $activityHistoryDeleteByNameService($dto); // NO RETURN

            return new JsonResponse(['message' => 'Activity deleted successfully'], 200);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Unexpected error'], 500);
        }
    }

}