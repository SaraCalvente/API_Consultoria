<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\Admin\ClientDeleteByEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientAdminDeleteController extends AbstractController
{
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