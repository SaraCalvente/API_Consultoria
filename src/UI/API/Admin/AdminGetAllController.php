<?php
declare(strict_types=1);

namespace App\UI\API\Admin;

use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\AdminFindAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AdminGetAllController extends AbstractController
{
    #[Route('/admins', name: 'get_admin_users', methods: ['GET'])]
    public function getAdminUsers(AdminFindAllService $adminFindAllService): JsonResponse
    {
        return $adminFindAllService();
    }

}