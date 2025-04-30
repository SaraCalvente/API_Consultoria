<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
class ActivityHistoryGetByProjectController extends AbstractController
{
    #[Route('/activity/project/activities', name: 'get_all_project_activities', methods: ['GET'])]
    #[OA\Get(
        path: "/project/activities",
        description: "Retrieve all activities for a project.",
        summary: "Get all project activities",
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
    public function getAllProjectTasks(Request $request, ActivityHistoryGetByProjectService $taskFindByProjectService): JsonResponse
    {
        $projectName = $request->query->get('projectName');
        $data = [
            'projectName' => $projectName];
        return $taskFindByProjectService($data);
    }
}