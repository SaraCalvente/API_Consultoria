<?php
declare(strict_types=1);

namespace App\UI\API\User\Notification;

use App\Project\Application\Task\TaskGetByConsultantService;
use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\Notification\NotificationGetByCreatorUserAndDateService;
use App\User\Application\Notification\NotificationGetByCreatorUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class NotificationGetByCreatorAndDateController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/notifications/user/created/date', name: 'get_creator_date_notifications', methods: ['GET'])]
    #[OA\Get(
        path: "/notifications/user/created/date",
        description: "Retrieve all created notifications for an authenticated user in a day.",
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
                description: "Not authenticated.",
            )
        ]
    )]
    public function getNotificationsByCreator(Security $security, Request $request, NotificationGetByCreatorUserAndDateService $findByCreatorUserService): JsonResponse
    {
        try {
            $date = $request->query->get('date');

            $data = [
                'date' => $date
            ];
            $user = $this->authChecker->getAuthenticated($security);
            return $findByCreatorUserService($user, $data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}