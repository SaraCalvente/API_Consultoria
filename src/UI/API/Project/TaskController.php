<?php

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectCreateService;
use App\Project\Application\Project\ProjectDeleteByNameService;
use App\Project\Application\Project\ProjectFindAllService;
use App\Project\Application\Project\ProjectFindByUserService;
use App\Project\Application\Project\ProjectUpdateByNameService;
use App\Project\Application\Task\TaskDeleteByNameService;
use App\Project\Application\Task\TaskUpdateByNameAndProjectService;
use App\Project\Application\Task\TaskCreateSevice;
use App\Project\Application\Task\TaskFindAllService;
use App\Project\Application\Task\TaskFindByConsultantService;
use App\Project\Application\Task\TaskFindByNameAndProjectService;
use App\Project\Application\Task\TaskFindByProjectService;
use App\Project\Application\Task\TaskFindByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TaskController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/task/create', name: 'task_create', methods: ['POST'])]
    public function createTask(
        Request $request, TaskCreateSevice $taskCreateService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['projectName', 'name', 'description', 'startDate', 'status', 'consultantsEmails'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }

            return $taskCreateService(
                $data['projectName'], $data['name'],
                $data['description'], $data['startDate'], $data['endDate'],
                $data['status'], $data['consultantsEmails']);

        } catch (\Exception $e){
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/user/tasks', name: 'get_user_tasks', methods: ['GET'])]
    public function getTasksByUser(Security $security, TaskFindByUserService $taskFindByUserService): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $taskFindByUserService($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/task', name: 'get_task_by_name_and_project', methods: ['GET'])]
    public function getTasksById(Request $request, TaskFindByNameAndProjectService $findByNameAndProjectService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $findByNameAndProjectService($data['name'], $data['projectName']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/admin/tasks', name: 'get_all_tasks', methods: ['GET'])]
    public function getAllTasks(TaskFindAllService $taskFindAllService): JsonResponse
    {
        return $taskFindAllService();
    }

    #[Route('/admin/consultant/tasks', name: 'get_all_consultant_tasks', methods: ['GET'])]
    public function getAllConsultantTasks(Request $request, TaskFindByConsultantService $taskFindByConsultant): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $taskFindByConsultant($data['email']);
    }

    #[Route('/admin/project/tasks', name: 'get_all_project_tasks', methods: ['GET'])]
    public function getAllProjectTasks(Request $request, TaskFindByProjectService $taskFindByProjectService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $taskFindByProjectService($data['projectName']);
    }

    #[Route('/task/update', name: 'task_update', methods: ['PUT'])]
    public function updateProject(Request $request, TaskUpdateByNameAndProjectService $tareaUpdateByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $tareaUpdateByNameService(
                $data['name'],
                $data['projectName'],
                $data['description'] ?? null,
                $data['status'] ?? null,
                $data['endDate'] ?? null,
                $data['addConsultantsEmails'] ?? null,
                $data['erraseConsultantsEmails'] ?? null,

            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/task/delete', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(Request $request, TaskDeleteByNameService $taskDeleteByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $taskDeleteByNameService($data['name'], $data['projectName']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}