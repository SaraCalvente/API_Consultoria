<?php
declare(strict_types=1);

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectFindAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProjectGetAllController extends AbstractController
{
    #[Route('/admin/projects', name: 'get_all_projects', methods: ['GET'])]
    public function getAllProjects(ProjectFindAllService $projectFindAllService): JsonResponse
    {
        return $projectFindAllService();
    }

}