<?php
declare(strict_types=1);

namespace App\UI\API\User\Notification;

use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\Notification\NotificationCreateService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class NotificationCreateController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \Exception
     */
    #[Route('/notification/create', name: 'notification_create', methods: ['POST'])]
    #[OA\Post(
        path: "/notification/create",
        description: "Create a new notification.",
        summary: "Notification creation.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["message", "date", "users"],
                properties: [
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
                    ),
                ],
                type: "object"
            )
        ),
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
    public function createNotification(
        Request $request, NotificationCreateService $notificationCreateService, Security $security
    ): JsonResponse {
        $user = $this->authChecker->getAuthenticated($security);
        $data = json_decode($request->getContent(), true);


        return $notificationCreateService($user, $data);
    }



}