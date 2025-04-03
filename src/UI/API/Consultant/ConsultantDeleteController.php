<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantDeleteByIdService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ConsultantDeleteController extends AbstractController
{

    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/consultant/delete', name: 'delete_consultant', methods: ['DELETE'])]
    public function deleteConsultant(Security $security, ConsultantDeleteByIdService $consultantDeleteByIdService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $consultantDeleteByIdService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}