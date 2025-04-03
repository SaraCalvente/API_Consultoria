<?php
declare(strict_types=1);

namespace App\UI\API\Task;

use App\Project\Application\Task\TaskFindAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TaskGetAllController extends AbstractController
{
    #[Route('/admin/tasks', name: 'get_all_tasks', methods: ['GET'])]
    public function getAllTasks(TaskFindAllService $taskFindAllService): JsonResponse
    {
        return $taskFindAllService();
    }

}