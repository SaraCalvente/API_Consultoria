<?php
declare(strict_types=1);

namespace App\UI\API\Project\Task;

use App\Project\Application\Task\TaskFindByConsultantService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

class TasksGetByConsultantController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/user/tasks', name: 'get_user_tasks', methods: ['GET'])]
    #[OA\Get(
        path: "/user/tasks",
        description: "Retrieve all tasks for an authenticated user.",
        summary: "Get all user tasks",
        responses: [
            new OA\Response(
                response: 200,
                description: "Tasks retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "tasks", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "task_id", type: "integer", example: 1),
                                new OA\Property(property: "name", type: "string", example: "This is the name of a task"),
                                new OA\Property(property: "description", type: "string", example: "This is a task description."),
                                new OA\Property(property: "project_id", type: "integer", example: 2),
                                new OA\Property(property: "startDate", type: "string", example: "2025-03-20 08:00:00"),
                                new OA\Property(property: "endDate", type: "string", example: "2025-03-20 08:00:00"),
                                new OA\Property(property: "status", type: "string", example: "Pendiente"),
                                new OA\Property(
                                    property: "consultantsIds",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "int", example: 3),
                                        ],
                                        type: "object"
                                    )
                                ),
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
    public function getTasksByUser(Security $security, TaskFindByConsultantService $taskFindByUserService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $taskFindByUserService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}