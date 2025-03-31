<?php

namespace App\UI\API\Project;

use App\Consultant\Application\ConsultantService;
use App\Project\Application\ProjectService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProjectController extends AbstractController
{
    private ProjectService $projectService;
    private AuthChecker $authChecker;

    public function __construct(ProjectService $projectService, authChecker $authChecker)
    {
        $this->projectService = $projectService;
        $this->authChecker = $authChecker;
    }

    #[Route('/project/create', name: 'project_create', methods: ['POST'])]
    public function createProject(
        Request $request
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

            return $this->projectService->createProject(
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

    #[Route('/projects', name: 'get_project', methods: ['GET'])]
    public function getProjectsByUser(Security $security): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $this->projectService->getProjectsByUser($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/admin/projects', name: 'get_all_projects', methods: ['GET'])]
    public function getAllConsultants(): JsonResponse
    {
        return $this->projectService->getAllProjects();
    }

    #[Route('/project/update', name: 'project_update', methods: ['PUT'])]
    public function updateProject(Request $request, Security $security): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $this->projectService->updateProject(
                $data['name'] ?? null,
                $data['description'] ?? null,
                $data['status'] ?? null,
                $data['endDate'] ?? null,
                $data['consultantsEmails'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/project/delete', name: 'delete_project', methods: ['DELETE'])]
    public function deleteProject(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $this->projectService->deleteProject($data['name']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
