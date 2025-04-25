<?php
declare(strict_types=1);

namespace App\UI\API\User\Notification;

use App\Project\Application\Task\TaskGetAllService;
use App\User\Application\Notification\NotificationGetAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class NotificationGetAllController extends AbstractController
{
    #[Route('/admin/notifications', name: 'get_all_notifications', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/notifications",
        description: "Retrieve all notifications for an authenticated admin.",
        summary: "Get all notifications",
        responses: [
            new OA\Response(
                response: 201,
                description: "Notification created successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Notification created successfully"),
                        new OA\Property(property: "creatorEmail", type: "string", example: "consultant@example.com"),
                        new OA\Property(property: "message", type: "string", example: "This is an example message."),
                        new OA\Property(property: "date", type: "string", example: "2025-03-23 08:00:00"),
                        new OA\Property(
                            property: "users",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "email", type: "string", example: "consultant@example.com"),
                                ],
                                type: "object"
                            )
                        ),                    ]
                )
            ),
            new OA\Response(
                response: 402,
                description: "Notification already exists"
            )
        ]
    )]
    public function getAllNotifications(NotificationGetAllService $findAllService): JsonResponse
    {
        return $findAllService();
    }

}