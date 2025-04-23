<?php

// src/UI/API/User/UserController.php

namespace App\UI\API\User;

use App\User\Application\UserLoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class UserLoginController extends AbstractController
{
    #[Route('/login', name: 'user_login', methods: ['POST'])]
    #[OA\Post(
        path: "/login",
        description: "Login a user.",
        summary: "User login.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                    new OA\Property(property: "password", type: "string", example: "SuperSecure123")
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Admin registered successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User logged in successfully"),
                        new OA\Property(property: "id", type: "integer", example: "2"),
                        new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...."),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Email and password are required and cannot be empty.",
            ),
            new OA\Response(
                response: 402,
                description: "User already exists"
            )
        ]
    )]

    public function login(Request $request, UserLoginService $loginService): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $validationErrors = $this->validateEmailAndPassword($request);

        if ($validationErrors !== null) {
            return new JsonResponse(['error' => $validationErrors], 400);
        }
        try{
            $response = $loginService($data);
            return $response;

        } catch (\Exception $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }
    }

    private function validateEmailAndPassword(Request $request): ?string
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || empty($data['email']) || !isset($data['password']) || empty($data['password'])) {
            return 'Email and password are required and cannot be empty';
        }
        return null;
    }

}
