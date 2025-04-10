<?php

namespace App\UI\API\Project\Project;

use App\Project\Application\Project\ProjectDeleteByNameService;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectDeleteController extends AbstractController
{

    #[Route('/project/delete', name: 'delete_project', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/project/delete",
        description: "Deletes the authenticated project.",
        summary: "Project deleted successfully",
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
                description: "Project deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Project deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Not authorized"
            ),
        ]
    )]
    public function deleteProject(Request $request, ProjectDeleteByNameService $projectDeleteByNameService): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            return $projectDeleteByNameService($data['name']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
