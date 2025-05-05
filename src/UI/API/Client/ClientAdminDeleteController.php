<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientDeleteByEmailService;
use App\Client\Domain\ClientDeleteDTO;
use App\Shared\Domain\Exception\UserNotFoundException;
use DomainException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientAdminDeleteController extends AbstractController
{
    #[Route('/admin/delete/client', name: 'admin_delete_client', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/admin/client/delete",
        description: "Deletes the client by an authenticated admin",
        summary: "Client deleted successfully",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "user@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Client deleted successfully"),
            new OA\Response(response: 401, description: "Not authorized"),
            new OA\Response(response: 402, description: "Cannot delete client because there are associated projects"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function adminDeleteClient(Request $request, ClientDeleteByEmailService $clientDeleteByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['error' => 'Missing email'], 400);
        }

        try {
            $dto = new ClientDeleteDTO($email);
            $clientDeleteByEmail($dto);

            return new JsonResponse(['message' => 'Client deleted successfully'], 200);

        } catch (UserNotFoundException $e) {
            return new JsonResponse(["message" => $e->getMessage()], 404);
        } catch (DomainException $e) {
            return new JsonResponse(["message" => $e->getMessage()], 402);
        } catch (\Throwable $e) {
            return new JsonResponse(["message" => "Unexpected error: " . $e->getMessage()], 500);
        }
    }
}