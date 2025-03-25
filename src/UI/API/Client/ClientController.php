<?php
namespace App\UI\API\Client;

use App\Client\Application\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    private ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }
    #[Route('/client/{id}', name: 'get_client_by_id', methods: ['GET'])]
    public function getClientById(int $id): JsonResponse
    {
        try {
            $clientData = $this->clientService->getClientById($id);
            return new JsonResponse($clientData, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/clients', name: 'get_all_clients', methods: ['GET'])]
    public function getAllClients(): JsonResponse
    {
        return $this->clientService->getAllClients();
    }

    #[Route('/client/{id}/delete', name: 'delete_client', methods: ['DELETE'])]
    public function deleteConsultant(int $id): JsonResponse
    {
        try {
            $this->clientService->deleteClient($id);
            return new JsonResponse(['message' => 'Consultant deleted successfully'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}