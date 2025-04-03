<?php
declare(strict_types=1);

namespace App\UI\API\Task;

use App\Project\Application\Task\TaskUpdateByNameAndProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskUpdateController extends AbstractController
{
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

}