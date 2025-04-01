<?php

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectCreateService;
use App\Project\Application\Task\TaskCreateSevice;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
}