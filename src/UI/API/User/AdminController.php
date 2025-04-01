<?php

namespace App\UI\API\User;

use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\AdminDeleteByIdService;
use App\User\Application\AdminFindAllService;
use App\User\Application\AdminRegisterService;
use App\User\Application\AdminService;
use App\User\Application\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }
    #[Route('/register/admin', name: 'admin_register', methods: ['POST'])]
    public function register(
        Request $request, AdminRegisterService $adminRegisterService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $adminRegisterService($data['email'], $data['password']);
    }

    #[Route('/admins', name: 'get_admin_users', methods: ['GET'])]
    public function getAdminUsers(AdminFindAllService $adminFindAllService): JsonResponse
    {
        return $adminFindAllService();
    }

    #[Route('/admin/delete', name: 'delete_admin', methods: ['DELETE'])]
    public function deleteAdmin( Security $security, AdminDeleteByIdService $adminDeleteByIdService): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $adminDeleteByIdService($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}