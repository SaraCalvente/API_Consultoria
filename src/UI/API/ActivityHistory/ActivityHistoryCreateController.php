<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\Project\Application\Task\TaskCreateSevice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ActivityHistoryCreateController extends AbstractController
{
    #[Route('/activity/create', name: 'activity_create', methods: ['POST'])]
    #[OA\Post(
        path: "/activity/create",
        description: "Create a new activity history.",
        summary: "Activity history creation.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["projectName", "name", "description", "date", "consultantEmail"],
                properties: [
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                    new OA\Property(property: "name", type: "string", example: "This is the name of a task"),
                    new OA\Property(property: "description", type: "string", example: "This is a task description."),
                    new OA\Property(property: "date", type: "string", example: "2025-03-20"),
                    new OA\Property(property: "userEmail", type: "string", example: "consultant@example.com"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Activities retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "activities", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "activity_id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "This is the name of a activity"),
                                new OA\Property(property: "description", type: "string", example: "This is a activity description."),
                                new OA\Property(property: "project_id", type: "integer", example: 2),
                                new OA\Property(property: "date", type: "string", example: "2025-03-20"),
                                new OA\Property(property: "user_id", type: "integer", example: 3)
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
    public function createActivityHistory(
        Request $request, ActivityHistoryCreateService $activityHistoryCreateService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['projectName', 'name', 'description', 'date', 'consultantEmail'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }

            return $activityHistoryCreateService(
                $data['name'], $data['description'], $data['date'],
                $data['projectName'], $data['consultantEmail']);

        } catch (\Exception $e){
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }

}