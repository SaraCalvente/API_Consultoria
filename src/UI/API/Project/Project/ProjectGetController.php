<?php
declare(strict_types=1);

namespace App\UI\API\Project\Project;

use App\Project\Application\Project\ProjectGetByNameService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectGetController extends AbstractController
{
    #[Route('/project', name: 'get_project', methods: ['GET'])]
    #[OA\Get(
        path: "/project",
        description: "Retrieve the project by name",
        summary: "Get the project",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "This is the name of a project."),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Project retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "project_id", type: "integer", example: 1),
                        new OA\Property(property: "client_id", type: "integer", example: 2),
                        new OA\Property(property: "name", type: "string", example: "Project 1"),
                        new OA\Property(property: "description", type: "string", example: "This is a project description."),
                        new OA\Property(property: "startDate", type: "string", example: "2025-03-20"),
                        new OA\Property(property: "endDate", type: "string", example: "2025-03-20"),
                        new OA\Property(property: "status", type: "string", example: "Pendiente"),
                        new OA\Property(property: "clientEmail", type: "string", example: "client@example.com"),
                        new OA\Property(
                            property: "consultantsIds",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 3),
                                ],
                                type: "object"
                            )
                        ),
                    ],
                    type: "object"

                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized"
            )]
    )]
    public function getProjectsByName(Request $request, ProjectGetByNameService $projectFindByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $projectFindByNameService($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}