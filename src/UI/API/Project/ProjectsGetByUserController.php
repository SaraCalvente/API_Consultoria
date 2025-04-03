<?php
declare(strict_types=1);

namespace App\UI\API\Project;

use App\Project\Application\Project\ProjectFindByClientService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ProjectsGetByUserController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(authChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/user/projects', name: 'get_user_projects', methods: ['GET'])]
    public function getProjectsByUser(Security $security, ProjectFindByClientService $projectFindByUserService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $projectFindByUserService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}