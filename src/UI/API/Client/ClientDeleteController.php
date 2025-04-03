<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientDeleteByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ClientDeleteController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    #[Route('/client/delete', name: 'delete_client', methods: ['DELETE'])]
    public function deleteClient (Security $security, ClientDeleteByUserService $clientDeleteById): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            return $clientDeleteById($user);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}