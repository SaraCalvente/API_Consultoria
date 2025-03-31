<?php
namespace App\UI\API\Client;

use App\Client\Application\ClientService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ClientController extends AbstractController
{
    private ClientService $clientService;
    private AuthChecker $authChecker;

    public function __construct(ClientService $clientService, AuthChecker $authChecker)
    {
        $this->clientService = $clientService;
        $this->authChecker = $authChecker;
    }

    #[Route('/register/client', name: 'client_register', methods: ['POST'])]
    public function register( Request $request ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $this->clientService->registerClient(
            $data['email'],
            $data['password'],
            $data['name'],
            $data['surnames'],
            $data['address'],
            $data['phoneNumber']);
    }
    #[Route('/client', name: 'get_client', methods: ['GET'])]
    public function getClient(Security $security): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $this->clientService->getClient($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/admin/clients', name: 'get_all_clients', methods: ['GET'])]
    public function getAllClients(): JsonResponse
    {
        return $this->clientService->getAllClients();
    }

    #[Route('/client/delete', name: 'delete_client', methods: ['DELETE'])]
    public function deleteClient (Security $security): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $this->clientService->deleteClientById($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/admin/client/delete', name: 'admin_delete_client', methods: ['DELETE'])]
    public function adminDeleteClient (Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            return $this->clientService->deleteClientByEmail($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @throws \Exception
     */
    #[Route('/client/update', name: 'client_update', methods: ['PUT'])]
    public function updateClient(Request $request, Security $security): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            $data = json_decode($request->getContent(), true);
            return $this->clientService->updateClientById(
                $userId,
                $data['address'] ?? null,
                $data['phoneNumber'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

    /**
     * @throws \Exception
     */
    #[Route('/admin/client/update', name: 'admin_client_update', methods: ['PUT'])]
    public function adminUpdateClient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $this->clientService->updateClientByEmail(
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['phoneNumber'] ?? null
        );
    }


}