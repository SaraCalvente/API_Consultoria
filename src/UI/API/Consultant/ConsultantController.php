<?php

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantController extends AbstractController
{
    private ConsultantService $consultantService;
    private AuthChecker $authChecker;

    public function __construct(ConsultantService $consultantService, AuthChecker $authChecker)
    {
        $this->consultantService = $consultantService;
        $this->authChecker = $authChecker;
    }

    #[Route('/register/consultant', name: 'consultant_register', methods: ['POST'])]
    public function register(
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $this->consultantService->registerConsultant(
            $data['email'],
            $data['password'],
            $data['name'],
            $data['surnames'],
            $data['profile']);
    }

    #[Route('/consultant', name: 'get_consultant', methods: ['GET'])]
    public function getConsultant(Security $security): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $this->consultantService->getConsultant($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/admin/consultants', name: 'get_all_consultants', methods: ['GET'])]
    public function getAllConsultants(): JsonResponse
    {
        return $this->consultantService->getAllConsultants();
    }

    #[Route('/consultant/delete', name: 'delete_consultant', methods: ['DELETE'])]
    public function deleteConsultant(Security $security): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $this->consultantService->deleteConsultantById($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/admin/consultant/delete', name: 'admin_delete_consultant', methods: ['DELETE'])]
    public function adminDeleteConsultant(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            return $this->consultantService->deleteConsultantByEmail($data['email']);
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
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            $data = json_decode($request->getContent(), true);
            return $this->consultantService->updateConsultantById(
                $userId,
                $data['profile'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

    #[Route('/admin/consultant/update', name: 'admin_consultant_update', methods: ['PUT'])]
    public function adminUpdateConsultant(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $this->consultantService->updateConsultantByEmail(
            $data['email'] ?? null,
            $data['profile'] ?? null
        );
    }


}
