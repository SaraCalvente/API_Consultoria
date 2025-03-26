<?php
// src/UI/API/Consultant/ConsultantController.php

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantService;
use Symfony\Component\Security\Core\Security;
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

    #[Route('/register/consultant', name: 'consultant_register', methods: ['POST'])]
    public function register(
        Request $request
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $this->consultantService->registerConsultant($data['email'], $data['password'], $data['name'], $data['surnames'], $data['profile']);
    }

    #[Route('/consultant', name: 'get_consultant', methods: ['GET'])]
    public function getConsultant(Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        $userId = $user->getId();
        try {
            $consultantData = $this->consultantService->getConsultant($userId);
            return new JsonResponse($consultantData, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/all/consultants', name: 'get_all_consultants', methods: ['GET'])]
    public function getAllConsultants(): JsonResponse
    {
        return $this->consultantService->getAllConsultants();
    }

    #[Route('/consultant/delete', name: 'delete_consultant', methods: ['DELETE'])]
    public function deleteConsultant(Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        $userId = $user->getId();
        try {
            return $this->consultantService->deleteConsultant($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @throws \Exception
     */
    #[Route('/consultant/update', name: 'consultant_update', methods: ['POST'])]
    public function updateConsultant(Request $request, Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $userId = $user->getId();

        return $this->consultantService->updateClient(
            $userId,
            $data['password'] ?? null,
            $data['address'] ?? null,
            $data['phoneNumber'] ?? null
        );
    }

}
