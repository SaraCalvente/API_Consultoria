<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByConsultantService;
use App\Project\Application\Task\TaskGetByConsultantService;
use App\Shared\Domain\Auth\AuthChecker;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class ActivityHistoryGetByConsultantController extends AbstractController
{
    public function __construct(private AuthChecker $authChecker)
    {
    }

    #[Route('/activity/consultant/activities', name: 'get_consultant_email_activities', methods: ['GET'])]
    #[OA\Get(
        path: "/activity/consultant/activities",
        description: "Retrieve all activities for an authenticated user.",
        summary: "Get all user activities",
        responses: [
            new OA\Response(
                response: 200,
                description: "Activities retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Activities retrieved successfully"),
                        new OA\Property(property: "current_consultant_id", type: "integer", example: 3),
                        new OA\Property(property: "activities", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "activity_id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "Activity name"),
                                new OA\Property(property: "description", type: "string", example: "Activity description"),
                                new OA\Property(property: "project_id", type: "integer", example: 2),
                                new OA\Property(property: "date", type: "string", example: "2025-03-20"),
                                new OA\Property(property: "user_id", type: "integer", example: 4)
                            ],
                            type: "object"
                        ))
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Consultant not found")
        ]
    )]
    public function getTasksByUser(
        Security $security,
        ActivityHistoryGetByConsultantService $taskFindByUserService
    ): JsonResponse {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            $result = $taskFindByUserService($user);

            return new JsonResponse([
                'message' => 'Activities retrieved successfully',
                'current_consultant_id' => $result['current_consultant_id'],
                'activities' => $result['activities']
            ]);
        } catch (DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }
}