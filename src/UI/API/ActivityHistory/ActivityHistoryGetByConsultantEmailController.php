<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByConsultantEmailService;
use App\Shared\Domain\Auth\AuthChecker;
use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ActivityHistoryGetByConsultantEmailController extends AbstractController
{
    #[Route('/admin/consultant/activities', name: 'get_consultant_activities', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/consultant/activities",
        description: "Retrieve all activities for a user by admin.",
        summary: "Get all user activities",
        parameters: [
            new OA\Parameter(
                name: "email",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "consultant@example.com"
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Activities retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Activities retrieved successfully"),
                        new OA\Property(property: "current_consultant_id", type: "integer", example: 5),
                        new OA\Property(property: "activities", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "activity_id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "This is the name of an activity"),
                                new OA\Property(property: "description", type: "string", example: "This is an activity description."),
                                new OA\Property(property: "project_id", type: "integer", example: 2),
                                new OA\Property(property: "date", type: "string", example: "2025-03-20"),
                                new OA\Property(property: "user_id", type: "integer", example: 3)
                            ],
                            type: "object"
                        ))
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Bad request"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 404, description: "Consultant not found")
        ]
    )]
    public function getTasksByUser(
        Request $request,
        ActivityHistoryGetByConsultantEmailService $taskFindByUserService
    ): JsonResponse {
        $email = $request->query->get('email');

        if (!$email) {
            return new JsonResponse(['error' => 'Email parameter is required'], 400);
        }

        try {
            $data = ['email' => $email];
            $result = $taskFindByUserService($data);

            return new JsonResponse([
                'message' => 'Activities retrieved successfully',
                'current_consultant_id' => $result['current_consultant_id'],
                'activities' => $result['activities']
            ], 200);
        } catch (DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Internal server error'], 500);
        }
    }
}