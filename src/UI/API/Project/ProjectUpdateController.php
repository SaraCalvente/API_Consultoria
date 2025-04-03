<?php
declare(strict_types=1);

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectUpdateByNameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectUpdateController extends AbstractController
{
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

}