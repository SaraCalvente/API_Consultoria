<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientFindByUserService;
use App\Client\Application\ClientRegisterService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ClientRegisterController extends AbstractController
{
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
}