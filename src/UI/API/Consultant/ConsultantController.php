<?php

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantDeleteByEmailService;
use App\Consultant\Application\ConsultantDeleteByIdService;
use App\Consultant\Application\ConsultantFindAllService;
use App\Consultant\Application\ConsultantFindByIdService;
use App\Consultant\Application\ConsultantRegisterService;
use App\Consultant\Application\ConsultantUpdateByEmailService;
use App\Consultant\Application\ConsultantUpdateByIdService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantController extends AbstractController
{

    private AuthChecker $authChecker;

    public function __construct( AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/register/consultant', name: 'consultant_register', methods: ['POST'])]
    public function register(
        Request $request, ConsultantRegisterService $consultantRegisterService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $consultantRegisterService(
            $data['email'],
            $data['password'],
            $data['name'],
            $data['surnames'],
            $data['profile']);
    }

    #[Route('/consultant', name: 'get_consultant', methods: ['GET'])]
    public function getConsultant(Security $security, ConsultantFindByIdService $consultantFindByIdService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $consultantFindByIdService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/admin/consultants', name: 'get_all_consultants', methods: ['GET'])]
    public function getAllConsultants(ConsultantFindAllService $consultantFindAllService): JsonResponse
    {
        return $consultantFindAllService();
    }

    /**
     * @throws \Exception
     */
    #[Route('/consultant/update', name: 'consultant_update', methods: ['PUT'])]
    public function updateConsultant(Request $request, Security $security, ConsultantUpdateByIdService $consultantUpdateById): JsonResponse
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

    #[Route('/admin/consultant/update', name: 'admin_consultant_update', methods: ['PUT'])]
    public function adminUpdateConsultant(Request $request, ConsultantUpdateByEmailService $consultantUpdateByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $consultantUpdateByEmail(
            $data['email'] ?? null,
            $data['profile'] ?? null
        );
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

    #[Route('/admin/consultant/delete', name: 'admin_delete_consultant', methods: ['DELETE'])]
    public function adminDeleteConsultant(Request $request, ConsultantDeleteByEmailService $consultantDeleteByEmailService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            return $consultantDeleteByEmailService($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }


}
