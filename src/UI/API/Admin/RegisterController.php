<?php
declare(strict_types=1);

namespace App\UI\API\Admin;

use App\Shared\Domain\Auth\AuthChecker;
use App\User\Application\AdminRegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
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
}