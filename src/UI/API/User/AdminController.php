<?php

namespace App\UI\API\User;

use App\User\Application\AdminService;
use App\User\Application\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }
    #[Route('/register/admin', name: 'admin_register', methods: ['POST'])]
    public function register(
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $this->adminService->registerAdmin($data['email'], $data['password']);
    }

    #[Route('/admins', name: 'get_admin_users', methods: ['GET'])]
    public function getAdminUsers(UserService $userService): JsonResponse
    {
        return $userService->getAdminUsers();
    }

    #[Route('/admin/{id}/delete', name: 'delete_admin', methods: ['DELETE'])]
    public function deleteAdmin(int $id): JsonResponse
    {
        try {
            return $this->userService->deleteAdmin($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}