<?php
// src/UI/API/Consultant/ConsultantController.php

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantController extends AbstractController
{
    private ConsultantService $consultantService;

    public function __construct(ConsultantService $consultantService)
    {
        $this->consultantService = $consultantService;
    }

    #[Route('/consultant/{id}', name: 'get_consultant_by_id', methods: ['GET'])]
    public function getConsultantById(int $id): JsonResponse
    {
        try {
            $consultantData = $this->consultantService->getConsultantById($id);
            return new JsonResponse($consultantData, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/consultants', name: 'get_all_consultants', methods: ['GET'])]
    public function getAllConsultants(): JsonResponse
    {
        return $this->consultantService->getAllConsultants();
    }

    #[Route('/consultant/{id}/delete', name: 'delete_consultant', methods: ['DELETE'])]
    public function deleteConsultant(int $id): JsonResponse
    {
        try {
            $this->consultantService->deleteConsultant($id);
            return new JsonResponse(['message' => 'Consultant deleted successfully'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
