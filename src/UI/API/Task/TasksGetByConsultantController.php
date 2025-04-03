<?php
declare(strict_types=1);

namespace App\UI\API\Task;

use App\Project\Application\Task\TaskFindByConsultantService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TasksGetByConsultantController extends AbstractController
{
    #[Route('/admin/consultant/tasks', name: 'get_all_consultant_tasks', methods: ['GET'])]
    public function getAllConsultantTasks(Request $request, TaskFindByConsultantService $taskFindByConsultant): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $taskFindByConsultant($data['email']);
    }

}