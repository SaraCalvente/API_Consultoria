<?php

namespace App\UI\API\User\Notification;

use App\Project\Application\Task\TaskDeleteByNameService;
use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\Notification\NotificationDeleteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Core\Security;

class NotificationDeleteController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }
    #[Route('/notification/delete', name: 'delete_notification', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/notification/delete",
        description: "Deletes the authenticated notification.",
        summary: "Notification deleted successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id"],
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 3),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Notification deleted successfully.",

            ),
            new OA\Response(
                response: 402,
                description: "Notification already exists"
            )
        ]
    )]
    public function deleteNotification(Security $security, Request $request, NotificationDeleteService $notificaionDeleteService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $this->authChecker->getAuthenticated($security);
            return $notificaionDeleteService($data['id'], $user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}