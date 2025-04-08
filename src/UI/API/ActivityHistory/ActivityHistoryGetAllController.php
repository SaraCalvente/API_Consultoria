<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryFindAllService;
use App\Project\Application\Task\TaskFindAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ActivityHistoryGetAllController extends AbstractController
{
    #[Route('/admin/activities', name: 'get_all_activities', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/activities",
        description: "Retrieve all activities for an authenticated admin.",
        summary: "Get all activities",
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
    public function getAllTasks(ActivityHistoryFindAllService $activityHistoryFindAllService): JsonResponse
    {
        return $activityHistoryFindAllService();
    }
}