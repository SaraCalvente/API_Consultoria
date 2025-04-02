<?php

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectCreateService;
use App\Project\Application\Project\ProjectDeleteByNameService;
use App\Project\Application\Project\ProjectFindAllService;
use App\Project\Application\Project\ProjectFindByNameService;
use App\Project\Application\Project\ProjectFindByUserService;
use App\Project\Application\Project\ProjectUpdateByNameService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProjectController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

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

    #[Route('/user/projects', name: 'get_user_projects', methods: ['GET'])]
    public function getProjectsByUser(Security $security, ProjectFindByUserService $projectFindByUserService): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $projectFindByUserService($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    #[Route('/project', name: 'get_project', methods: ['GET'])]
    public function getProjectsByName(Request $request, ProjectFindByNameService $projectFindByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $projectFindByNameService($data['name']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/admin/projects', name: 'get_all_projects', methods: ['GET'])]
    public function getAllConsultants(ProjectFindAllService $projectFindAllService): JsonResponse
    {
        return $projectFindAllService();
    }

    #[Route('/project/update', name: 'project_update', methods: ['PUT'])]
    public function updateProject(Request $request, ProjectUpdateByNameService $projectUpdateByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $projectUpdateByNameService(
                $data['name'],
                $data['description'] ?? null,
                $data['status'] ?? null,
                $data['endDate'] ?? null,
                $data['addConsultantsEmails'] ?? null,
                $data['erraseConsultantEmails'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/project/delete', name: 'delete_project', methods: ['DELETE'])]
    public function deleteProject(Request $request, ProjectDeleteByNameService $projectDeleteByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $projectDeleteByNameService($data['name']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
