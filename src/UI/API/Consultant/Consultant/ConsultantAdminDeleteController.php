<?php
declare(strict_types=1);

namespace App\UI\API\Consultant\Consultant;

use App\Consultant\Application\Consultant\Admin\ConsultantDeleteByEmailService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantAdminDeleteController extends AbstractController
{
    #[Route('/admin/consultant/delete', name: 'admin_delete_consultant', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/admin/consultant/delete",
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
        $data = json_decode($request->getContent(), true);
        try {
            return $consultantDeleteByEmailService($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}