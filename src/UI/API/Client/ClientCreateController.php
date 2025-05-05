<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientCreateService;
use App\Client\Domain\ClientCreateDTO;
use App\Client\Domain\ClientCreateRequestDTO;
use App\Shared\Domain\Exception\NotValidEmailException;
use App\Shared\Domain\Exception\NotValidPasswordLengthException;
use App\Shared\Domain\Exception\UserAlreadyExistsException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ClientCreateController extends AbstractController
{
    #[Route('/create/client', name: 'create_client', methods: ['POST'])]
    #[OA\Post(
        path: "/create/client",
        description: "Register a new client user.",
        summary: "Client register.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password", "name", "surnames", "address", "phoneNumber"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", example: "SecurePassword123"),
                    new OA\Property(property: "name", type: "string", example: "Ana"),
                    new OA\Property(property: "surnames", type: "string", example: "Garcia Ruiz"),
                    new OA\Property(property: "address", type: "string", example: "C/ Example, 9"),
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
                        new OA\Property(property: "address", type: "string", example: "C/ Example, 9"),
                        new OA\Property(property: "phoneNumber", type: "string", example: "666 666 666"),
                        new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string"))
                    ]
                )
            ),
            new OA\Response(response: 400, description: "Missing or invalid fields."),
            new OA\Response(response: 409, description: "User already exists.")
        ]
    )]
    public function clienteRegister(Request $request, ClientCreateService $clientRegister): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['email', 'password', 'name', 'surnames', 'address', 'phoneNumber'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field] ?? null)) {
                    throw new BadRequestHttpException("Missing or empty field: $field");
                }
            }

            $dto = new ClientCreateDTO(
                email: $data['email'],
                password: $data['password'],
                name: $data['name'],
                surnames: $data['surnames'],
                address: $data['address'],
                phoneNumber: $data['phoneNumber']
            );

            $clientDTO = $clientRegister($dto);

            return new JsonResponse([
                'message' => 'Client successfully registered',
                'id' => $clientDTO->clientId,
                'user_id' => $clientDTO->userId,
                'email' => (string) $clientDTO->email,
                'name' => $clientDTO->name,
                'surnames' => $clientDTO->surnames,
                'address' => $clientDTO->address,
                'phoneNumber' => $clientDTO->phoneNumber,
                'roles' => $clientDTO->roles
            ], 201);

        } catch (UserAlreadyExistsException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 409);
        } catch (NotValidEmailException|NotValidPasswordLengthException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
