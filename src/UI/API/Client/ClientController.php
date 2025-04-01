<?php
namespace App\UI\API\Client;

use App\Client\Application\ClientDeleteByEmailService;
use App\Client\Application\ClientDeleteByIdService;
use App\Client\Application\ClientFindAllService;
use App\Client\Application\ClientFindByIdService;
use App\Client\Application\ClientRegisterService;
use App\Client\Application\ClientService;
use App\Client\Application\ClientUpdateByEmailService;
use App\Client\Application\ClientUpdateByIdService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ClientController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/register/client', name: 'client_register', methods: ['POST'])]
    public function register(Request $request, ClientRegisterService $clientRegister ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $clientRegister(
            $data['email'],
            $data['password'],
            $data['name'],
            $data['surnames'],
            $data['address'],
            $data['phoneNumber']);
    }
    #[Route('/client', name: 'get_client', methods: ['GET'])]
    public function getClient(Security $security, ClientFindByIdService $clientFindService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticatedUserId($security);
            return $clientFindService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }

    #[Route('/admin/clients', name: 'get_all_clients', methods: ['GET'])]
    public function getAllClients(ClientFindAllService $clientFindAllService): JsonResponse
    {
        return $clientFindAllService();
    }

    /**
     * @throws \Exception
     */
    #[Route('/client/update', name: 'client_update', methods: ['PUT'])]
    public function updateClient(Request $request, Security $security, ClientUpdateByIdService $clientUpdateById): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            $data = json_decode($request->getContent(), true);
            return $clientUpdateById(
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
    public function adminUpdateClient(Request $request, ClientUpdateByEmailService $clientUpdateByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return $clientUpdateByEmail(
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['phoneNumber'] ?? null
        );
    }

    #[Route('/client/delete', name: 'delete_client', methods: ['DELETE'])]
    public function deleteClient (Security $security, ClientDeleteByIdService $clientDeleteById): JsonResponse
    {
        try {
            $userId = $this->authChecker->getAuthenticatedUserId($security);
            return $clientDeleteById($userId);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/admin/client/delete', name: 'admin_delete_client', methods: ['DELETE'])]
    public function adminDeleteClient (Request $request, ClientDeleteByEmailService $clientDeleteByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            return $clientDeleteByEmail($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }




}