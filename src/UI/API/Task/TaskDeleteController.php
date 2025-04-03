<?php

namespace App\UI\API\Task;

use App\Project\Application\Task\TaskDeleteByNameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskDeleteController extends AbstractController
{
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