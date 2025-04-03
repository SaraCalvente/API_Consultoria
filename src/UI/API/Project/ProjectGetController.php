<?php
declare(strict_types=1);

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectFindByNameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectGetController extends AbstractController
{
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

}