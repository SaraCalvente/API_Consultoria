<?php
declare(strict_types=1);

namespace App\UI\API\User\Notification;

use App\User\Application\Notification\NotificationAdminGetByReceiverUserAndDateService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotificationAdminGetByReceiverAndDateController extends AbstractController
{
    #[Route('/admin/notifications/user/received/date', name: 'get_received_date_notifications_admin', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/notifications/user/received/date",
        description: "Retrieve all received notifications for a user by an authenticated admin in a day.",
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
    public function getNotificationsByCreator(Request $request, NotificationAdminGetByReceiverUserAndDateService $findByReceiverUserService): JsonResponse
    {
        try {
            $email = $request->query->get('email');
            $date = $request->query->get('date');

            $data = [
                'email' => $email,
                'date' => $date
            ];
            return $findByReceiverUserService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}