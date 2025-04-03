<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientFindByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ClientGetController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }


    #[Route('/client', name: 'get_client', methods: ['GET'])]
    public function getClient(Security $security, ClientFindByUserService $clientFindService): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $clientFindService($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 404);
        }
    }
}