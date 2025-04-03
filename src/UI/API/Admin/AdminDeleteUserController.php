<?php

namespace App\UI\API\Admin;

use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\AdminDeleteByIdService;
use App\User\Application\AdminFindAllService;
use App\User\Application\AdminRegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminDeleteUserController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/admin/delete', name: 'delete_admin', methods: ['DELETE'])]
    public function deleteAdmin( Security $security, AdminDeleteByIdService $adminDeleteByIdService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $adminDeleteByIdService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}