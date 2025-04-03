<?php
declare(strict_types=1);

namespace App\UI\API\Task;

use App\Project\Application\Task\TaskFindByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TasksGetByUserController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/user/tasks', name: 'get_user_tasks', methods: ['GET'])]
    public function getTasksByUser(Security $security, TaskFindByUserService $taskFindByUserService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $taskFindByUserService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}