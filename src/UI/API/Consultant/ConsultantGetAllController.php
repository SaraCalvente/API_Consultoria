<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\Consultant\Admin\ConsultantFindAllService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantGetAllController extends AbstractController
{
    #[Route('/admin/consultants', name: 'get_all_consultants', methods: ['GET'])]
    #[OA\Get(
        path: "/admin/consultants",
        description: "Retrieve all consultants for an authenticated admin.",
        summary: "Get all consultants",
        responses: [
            new OA\Response(
                response: 200,
                description: "Consultants retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "consultants", type: "array", items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "user_id", type: "integer", example: 1),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "name", type: "string", example: "Ana"),
                                new OA\Property(property: "surNames", type: "string", example: "Garcia Ruiz"),
                                new OA\Property(property: "profile", type: "string", example: "Desarrollador"),
                                new OA\Property(property: "roles", type: "string", example: "ROLE_CLIENT"),

                            ],
                            type: "object"
                        ))
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )]
    )]
    public function getAllConsultants(ConsultantFindAllService $consultantFindAllService): JsonResponse
    {
        return $consultantFindAllService();
    }


}