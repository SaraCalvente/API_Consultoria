<?php
declare(strict_types=1);

namespace App\UI\API\Task;

use App\Project\Application\Task\TaskFindByProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TasksGetByProjectController extends AbstractController
{
    #[Route('/admin/project/tasks', name: 'get_all_project_tasks', methods: ['GET'])]
    public function getAllProjectTasks(Request $request, TaskFindByProjectService $taskFindByProjectService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $taskFindByProjectService($data['projectName']);
    }

}