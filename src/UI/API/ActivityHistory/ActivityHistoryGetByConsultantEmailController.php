<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryGetByConsultantEmailService;
use App\Shared\Domain\Auth\AuthChecker;
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "consultant@example.com"),
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
    public function getTasksByUser(Request $request, ActivityHistoryGetByConsultantEmailService $taskFindByUserService): JsonResponse
    {
        try {
            $email = $request->query->get('email');
            $data = [
                'email' => $email];
            return $taskFindByUserService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}