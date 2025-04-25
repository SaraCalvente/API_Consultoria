<?php
declare(strict_types=1);

namespace App\UI\API\User\Notification;

use App\User\Application\Notification\NotificationAdminGetByReceiverUserService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationAdminGetByReceiverController extends AbstractController
{

    #[Route('/admin/notifications/user/received', name: 'get_received_notifications_admin', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/notifications/user/received",
        description: "Retrieve all received notifications for a user by an authenticated admin.",
        summary: "Get all user notifications",
        responses: [
            new OA\Response(
                response: 201,
                description: "Notification created successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Notification retrieved successfully"),
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
                description: "Not authenticated"
            )
        ]
    )]
    public function getNotificationsByCreator(Request $request, NotificationAdminGetByReceiverUserService $findByReceiverUserService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $findByReceiverUserService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}