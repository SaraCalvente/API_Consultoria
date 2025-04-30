<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Consultant;

use App\Consultant\Application\Consultant\ConsultantCreateService;
use App\Shared\Domain\Exception\NotValidEmailException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantCreateController extends AbstractController
{

    /**
     * @throws NotValidEmailException
     */
    #[Route('/create/consultant', name: 'consultant_create', methods: ['POST'])]
    #[OA\Post(
        path: "/create/consultant",
        description: "Register a new consultant user.",
        summary: "Consultant register.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password", "name", "surNames", "profile"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", example: "SecurePassword123"),
                    new OA\Property(property: "name", type: "string", example: "Ana"),
                    new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                    new OA\Property(property: "profile", type: "string", example: "Desarrollador"),
                    new OA\Property(property: "abilityName", type: "string", example: "Habilidad 1"),
                    new OA\Property(property: "level", type: "string", example: "Alto"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Consultant registered successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Consultant registered successfully"),
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "user_id", type: "integer", example: 1),
                        new OA\Property(property: "email", type: "string", example: "user@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Ana"),
                        new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                        new OA\Property(property: "profile", type: "string", example: "Desarrollador"),
                        new OA\Property(property: "abilityName", type: "string", example: "Habilidad 1"),
                        new OA\Property(property: "level", type: "string", example: "Alto"),
                        new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT"),
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
    public function register(
        Request $request, ConsultantCreateService $consultantRegisterService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $validationErrors = $this->validateEmailAndPassword($request);

        if ($validationErrors !== null) {
            return new JsonResponse(['error' => $validationErrors], 400);
        }

        return $consultantRegisterService($data);
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