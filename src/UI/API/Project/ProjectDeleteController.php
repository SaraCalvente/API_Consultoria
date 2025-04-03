<?php

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectCreateService;
use App\Project\Application\Project\ProjectDeleteByNameService;
use App\Project\Application\Project\ProjectFindAllService;
use App\Project\Application\Project\ProjectFindByNameService;
use App\Project\Application\Project\ProjectFindByClientService;
use App\Project\Application\Project\ProjectUpdateByNameService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProjectDeleteController extends AbstractController
{

    #[Route('/project/delete', name: 'delete_project', methods: ['DELETE'])]
    public function deleteProject(Request $request, ProjectDeleteByNameService $projectDeleteByNameService, ProjectFindByNameService $projectFindByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $projectDeleteByNameService($data['name']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
