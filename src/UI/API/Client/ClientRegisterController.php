<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientRegisterService;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\Shared\Domain\Exception\NotValidPasswordLengthException;
use App\Shared\Domain\Exception\RequiredFieldException;
use App\Shared\Domain\Exception\UserAlreadyExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
class ClientRegisterController extends AbstractController
{
    #[Route('/register/client', name: 'client_register', methods: ['POST'])]
    #[OA\Post(
        path: "/register/client",
        description: "Register a new client user.",
        summary: "Client register.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password", "name", "surNames", "address", "phone_number"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", example: "SecurePassword123"),
                    new OA\Property(property: "name", type: "string", example: "Ana"),
                    new OA\Property(property: "surnames", type: "string", example: "Garcia Ruiz"),
                    new OA\Property(property: "address", type: "string", example: "C/ example, 9"),
                    new OA\Property(property: "phoneNumber", type: "string", example: "666 666 666"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Client registered successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Client registered successfully"),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", example: 1),
                        new OA\Property(property: "email", type: "string", example: "user@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Ana"),
                        new OA\Property(property: "surnames", type: "string", example: "Garcia Ruiz"),
                        new OA\Property(property: "address", type: "string", example: "C/ example, 9"),
                        new OA\Property(property: "phoneNumber", type: "string", example: "666 666 666"),
                        new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT"),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Email and password are required and cannot be empty.",
            ),
            new OA\Response(
                response: 409,
                description: "User already exists"
            ),
            new OA\Response(
                response: 400,
                description: "Invalid email."
            ),
            new OA\Response(
                response: 400,
                description: "Invalid password."
            )
        ]
    )]
    public function clienteRegister(Request $request, ClientRegisterService $clientRegister ): JsonResponse {

        try {
            $data = json_decode($request->getContent(), true);
            return $clientRegister($data);
        } catch (UserAlreadyExistsException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        } catch (NotValidEmailException|NotValidPasswordLengthException|RequiredFieldException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

    }


}