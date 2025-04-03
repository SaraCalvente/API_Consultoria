<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientUpdateByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ClientUpdateController extends AbstractController
{
    private AuthChecker $authChecker;

    public function __construct(AuthChecker $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    /**
     * @throws \Exception
     */
    #[Route('/client/update', name: 'client_update', methods: ['PUT'])]
    public function updateClient(Request $request, Security $security, ClientUpdateByUserService $clientUpdateById): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            $data = json_decode($request->getContent(), true);
            return $clientUpdateById(
                $user,
                $data['address'] ?? null,
                $data['phone_number'] ?? null
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 401);
        }
    }

}