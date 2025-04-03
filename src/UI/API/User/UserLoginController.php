<?php

// src/UI/API/User/UserController.php

namespace App\UI\API\User;

use App\User\Application\UserLoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserLoginController extends AbstractController
{
    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request, UserLoginService $loginService): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }
        try{
            $response = $loginService($data['email'], $data['password']);
            return $response;

        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }
    }

}
