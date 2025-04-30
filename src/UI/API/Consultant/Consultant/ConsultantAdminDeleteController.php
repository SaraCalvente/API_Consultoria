<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Consultant;

use App\Consultant\Application\Consultant\ConsultantDeleteByEmailService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantAdminDeleteController extends AbstractController
{
    #[Route('/admin/delete/consultant', name: 'admin_delete_consultant', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/admin/delete/consultant",
        description: "Deletes the consultant by an authenticated admin",
        summary: "Consultant deleted successfully",
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
            new OA\Response(
                response: 200,
                description: "Consultant deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Consultant deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Not authorized"
            ),
        ]
    )]
    public function adminDeleteConsultant(Request $request, ConsultantDeleteByEmailService $consultantDeleteByEmailService): JsonResponse
    {
        $email = $request->query->get('email');

        $data = [
            'email' => $email,
        ];
        try {
            return $consultantDeleteByEmailService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}