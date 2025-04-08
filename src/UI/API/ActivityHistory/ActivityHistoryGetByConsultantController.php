<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryFindByConsultantService;
use App\Project\Application\Task\TaskFindByConsultantService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class ActivityHistoryGetByConsultantController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/consultant/activities', name: 'get_consultant_email_activities', methods: ['GET'])]
    #[OA\Get(
        path: "/consultant/activities",
        description: "Retrieve all activities for an authenticated user.",
        summary: "Get all user activities",
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
    public function getTasksByUser(Security $security, ActivityHistoryFindByConsultantService $taskFindByUserService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $taskFindByUserService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}