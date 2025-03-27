<?php

namespace App\UI\API\Project;

use App\Consultant\Application\ConsultantService;
use App\Project\Application\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProjectController extends AbstractController
{
    private ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    #[Route('/create/project', name: 'project_create', methods: ['POST'])]
    public function register(
        Request $request
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['name', 'description', 'startDate', 'endDate', 'status', 'clientEmail', 'consultantsIds'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }

            return $this->projectService->createProject(
                $data['clientEmail'], $data['name'],
                $data['description'], $data['startDate'],
                $data['endDate'], $data['status'],
                $data['consultantsIds']);

        } catch (\Exception $e){
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    #[Route('/consultant', name: 'get_consultant', methods: ['GET'])]
    public function getConsultant(Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        $userId = $user->getId();
        try {
            $consultantData = $this->consultantService->getConsultant($userId);
            return new JsonResponse($consultantData, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/all/consultants', name: 'get_all_consultants', methods: ['GET'])]
    public function getAllConsultants(): JsonResponse
    {
        return $this->consultantService->getAllConsultants();
    }

    #[Route('/consultant/delete', name: 'delete_consultant', methods: ['DELETE'])]
    public function deleteConsultant(Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        $userId = $user->getId();
        try {
            return $this->consultantService->deleteConsultant($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @throws \Exception
     */
    #[Route('/consultant/update', name: 'consultant_update', methods: ['PUT'])]
    public function updateConsultant(Request $request, Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $userId = $user->getId();

        return $this->consultantService->updateConsultant(
            $userId,
            $data['profile'] ?? null,
        );
    }

}
