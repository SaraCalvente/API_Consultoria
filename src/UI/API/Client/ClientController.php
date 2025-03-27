<?php
namespace App\UI\API\Client;

use App\Client\Application\ClientService;
use App\User\Application\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ClientController extends AbstractController
{
    private ClientService $clientService;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(ClientService $clientService, JWTTokenManagerInterface $jwtManager)
    {
        $this->clientService = $clientService;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/register/client', name: 'client_register', methods: ['POST'])]
    public function register( Request $request ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $this->clientService->registerClient($data['email'], $data['password'], $data['name'], $data['surnames'], $data['address'], $data['phoneNumber']);
    }
    #[Route('/client', name: 'get_client', methods: ['GET'])]
    public function getClient(Security $security): JsonResponse
    {
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        $userId = $user->getId();
        try {
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
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }
        $userId = $user->getId();
        try {
            return $this->clientService->deleteClient($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/admin/client/delete', name: 'admin_delete_client', methods: ['DELETE'])]
    public function adminDeleteClient (Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            return $this->clientService->adminDeleteClient($data['email']);
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
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $userId = $user->getId();

        return $this->clientService->updateClient(
            $userId,
            $data['address'] ?? null,
            $data['phoneNumber'] ?? null
        );
    }

    /**
     * @throws \Exception
     */
    #[Route('/admin/client/update', name: 'admin_client_update', methods: ['PUT'])]
    public function adminUpdateClient(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $this->clientService->adminUpdateClient(
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['phoneNumber'] ?? null
        );
    }

}