<?php
declare(strict_types=1);

namespace App\UI\API\User\Notification;

use App\Project\Application\Task\TaskGetByConsultantService;
use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\Notification\NotificationGetByCreatorUserService;
use App\User\Application\Notification\NotificationGetByReceiverUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class NotificationGetByReceiverController extends AbstractController
{
    public function __construct(private AuthChecker $authChecker)
    {
    }

    #[Route('/notifications/user/received', name: 'get_received_notifications', methods: ['GET'])]
    #[OA\Get(
        path: "/notifications/user/received",
        description: "Retrieve all received notifications for an authenticated user.",
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
    public function getNotificationsByCreator(Security $security, NotificationGetByReceiverUserService $findByReceiverUserService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $findByReceiverUserService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}