<?php
declare(strict_types=1);

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectCreateService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectCreateController extends AbstractController
{
    #[Route('/project/create', name: 'project_create', methods: ['POST'])]
    public function createProject(
        Request $request, ProjectCreateService $projectCreateService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['name', 'description', 'startDate', 'status', 'clientEmail', 'consultantsEmails'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }

            $endDate = $data['endDate'] ?? null;

            return $projectCreateService(
                $data['clientEmail'], $data['name'],
                $data['description'], $data['startDate'], $endDate,
                $data['status'], $data['consultantsEmails']);

        } catch (\Exception $e){
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }


}