<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryUpdateByNameAndProjectService;
use App\Project\Application\Task\TaskUpdateByNameAndProjectService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\Security;

class ActivityHistoryUpdateController extends AbstractController
{
    public function __construct(private AuthChecker $authChecker)
    {
    }

    #[Route('/activity/update', name: 'activity_update', methods: ['PUT'])]
    #[OA\Put(
        path: "/activity/update",
        description: "Update activity details by name.",
        summary: "Activity details updated successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "projectName"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Task name"),
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                    new OA\Property(property: "description", type: "string", example: "New description"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Activity updated successfully",
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
    public function updateProject(Security $security, Request $request, ActivityHistoryUpdateByNameAndProjectService $activityHistoryUpdateController): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            $data = json_decode($request->getContent(), true);
            return $activityHistoryUpdateController($user, $data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}