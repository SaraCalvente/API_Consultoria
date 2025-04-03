<?php
declare(strict_types=1);

namespace App\UI\API\Task;

use App\Project\Application\Task\TaskFindByNameAndProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskGetController extends AbstractController
{
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

}