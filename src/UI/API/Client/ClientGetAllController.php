<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\Admin\ClientFindAllService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ClientGetAllController extends AbstractController
{
    #[Route('/admin/clients', name: 'get_all_clients', methods: ['GET'])]
    public function getAllClients(ClientFindAllService $clientFindAllService): JsonResponse
    {
        return $clientFindAllService();
    }

}