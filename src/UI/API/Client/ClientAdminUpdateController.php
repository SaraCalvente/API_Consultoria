<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\Admin\ClientUpdateByEmailService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientAdminUpdateController extends AbstractController
{
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


}