<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantFindByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ConsultantGetController extends AbstractController
{

    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/consultant', name: 'get_consultant', methods: ['GET'])]
    public function getConsultant(Security $security, ConsultantFindByUserService $consultantFindByIdService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $consultantFindByIdService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }


}