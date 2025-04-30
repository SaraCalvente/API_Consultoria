<?php
declare(strict_types=1);

namespace App\UI\API\User\Notification;

use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\Notification\NotificationGetByReceiverUserAndDateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class NotificationGetByReceiverAndDateController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/notifications/user/received/date', name: 'get_received_date_notifications', methods: ['GET'])]
    #[OA\Get(
        path: "/notifications/user/received/date",
        description: "Retrieve all received notifications for an authenticated user in a day.",
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
    public function getNotificationsByCreator(Security $security, Request $request, NotificationGetByReceiverUserAndDateService $findByReceiverUserService): JsonResponse
    {
        try {
            $date = $request->query->get('date');

            $data = [
                'date' => $date
            ];
            $user = $this->authChecker->getAuthenticated($security);
            return $findByReceiverUserService($user, $data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}