<?php
declare(strict_types=1);

namespace App\UI\API\Admin;

use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use App\User\Application\AdminRegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


class AdminRegisterController extends AbstractController
{
    #[Route('/register/admin', name: 'admin_register', methods: ['POST'])]

    #[OA\Post(
        path: "/register/admin",
        description: "Register a new admin user.",
        summary: "Administrator register.",
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
                        new OA\Property(property: "message", type: "string", example: "Admin registered successfully"),
                        new OA\Property(property: "id", type: "int", example: "2"),
                        new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                        new OA\Property(property: "roles", type: "string", example: "ROLE_ADMIN")
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

    #[Security(name: "Bearer")]
    public function register(
        Request $request, AdminRegisterService $adminRegisterService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $validationErrors = $this->validateEmailAndPassword($request);

        if ($validationErrors !== null) {
            return new JsonResponse(['error' => $validationErrors], 400);
        }

        return $adminRegisterService($data);
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