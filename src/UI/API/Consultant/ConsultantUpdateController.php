<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantUpdateByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ConsultantUpdateController extends AbstractController
{

    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @throws \Exception
     */
    #[Route('/consultant/update', name: 'consultant_update', methods: ['PUT'])]
    public function updateConsultant(Request $request, Security $security, ConsultantUpdateByUserService $consultantUpdateById): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            $data = json_decode($request->getContent(), true);
            return $consultantUpdateById(
                $user,
                $data['profile'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}