<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByNameAndProjectService;
use App\Project\Application\Task\TaskGetByNameAndProjectService;
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "projectName"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "This is the name of a activity"),
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                ],
                type: "object"
            )
        ),
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
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )]
    )]
    public function getActivityByNameAndProject(Request $request, ActivityHistoryGetByNameAndProjectService $findByNameAndProjectService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $findByNameAndProjectService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}