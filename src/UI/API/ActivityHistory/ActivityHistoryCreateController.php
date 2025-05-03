<?php
declare(strict_types=1);

namespace App\UI\API\ActivityHistory;

use App\ActivityHistory\Application\ActivityHistoryCreateService;
use App\ActivityHistory\Domain\DTO\ActivityHistoryCreateDTO;
use DomainException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivityHistoryCreateController extends AbstractController
{
    #[Route('/activity/create', name: 'activity_create', methods: ['POST'])]
    #[OA\Post(
        path: "/activity/create",
        description: "Create a new activity history.",
        summary: "Activity history creation.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["projectName", "name", "description", "date", "consultantEmail"],
                properties: [
                    new OA\Property(property: "projectName", type: "string", example: "Project 1"),
                    new OA\Property(property: "name", type: "string", example: "Write unit tests"),
                    new OA\Property(property: "description", type: "string", example: "Write unit tests for the new service."),
                    new OA\Property(property: "date", type: "string", format: "date", example: "2025-03-20"),
                    new OA\Property(property: "consultantEmail", type: "string", example: "consultant@example.com"),
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Activity created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "ActivityHistory created successfully"),
                        new OA\Property(property: "activity", properties: [
                            new OA\Property(property: "activityHistoryId", type: "integer", example: 1),
                            new OA\Property(property: "name", type: "string", example: "Write unit tests"),
                            new OA\Property(property: "description", type: "string", example: "Write unit tests for the new service."),
                            new OA\Property(property: "projectId", type: "integer", example: 2),
                            new OA\Property(property: "date", type: "string", example: "2025-03-20"),
                            new OA\Property(property: "userId", type: "integer", example: 3)
                        ], type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Missing required field"
            ),
            new OA\Response(
                response: 403,
                description: "Activity already exists"
            ),
            new OA\Response(
                response: 404,
                description: "Project or Consultant not found"
            ),
            new OA\Response(
                response: 500,
                description: "Unexpected error"
            )
        ]
    )]


    public function __invoke(
        Request $request,
        ActivityHistoryCreateService $activityHistoryCreateService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $requiredFields = ['projectName', 'name', 'description', 'date', 'consultantEmail'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse(['error' => "Missing required field: $field"], 400);
                }
            }

            $inputDTO = new ActivityHistoryCreateDTO(
                $data['projectName'],
                $data['name'],
                $data['description'],
                $data['date'],
                $data['consultantEmail'],
            );

            $outputDTO = $activityHistoryCreateService($inputDTO);

            return new JsonResponse([
                'message' => 'ActivityHistory created successfully',
                'activity' => [
                    'activityHistoryId' => $outputDTO->activityHistoryId,
                    'name' => $outputDTO->name,
                    'description' => $outputDTO->description,
                    'projectId' => $outputDTO->projectId,
                    'date' => $outputDTO->date,
                    'userId' => $outputDTO->userId,
                ]
            ], 201);

        } catch (DomainException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 403);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }

}