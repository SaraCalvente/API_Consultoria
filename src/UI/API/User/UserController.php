<?php

// src/UI/API/User/UserController.php

namespace App\UI\API\User;

use App\Client\Application\ClientService;
use App\Consultant\Domain\Profile;
use App\User\Application\UserFindAllService;
use App\User\Application\UserLoginService;
use App\User\Application\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
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

    #[Route('/users', name: 'get_all_users', methods: ['GET'])]
    public function getUsers(UserFindAllService $userFindAllService): JsonResponse
    {
        return $userFindAllService();
    }


}
