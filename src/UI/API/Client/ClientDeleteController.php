<?php
declare(strict_types=1);

namespace App\UI\API\Client;

use App\Client\Application\ClientDeleteByUserService;
use App\Shared\Domain\Auth\AuthChecker;
use App\Shared\Domain\Exception\NoDataToUpdateException;
use App\Shared\Domain\Exception\UserNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use OpenApi\Attributes as OA;

final  class ClientDeleteController extends AbstractController
{
    public function __construct(private AuthChecker $authChecker) {}

    #[Route('/client/delete', name: 'delete_client', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/client/delete",
        description: "Deletes the authenticated client.",
        summary: "Client deleted successfully",
        responses: [
            new OA\Response(
                response: 200,
                description: "Client deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Client deleted successfully")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Not authorized"),
            new OA\Response(response: 402, description: "Cannot delete client because there are associated projects"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function deleteClient(Security $security, ClientDeleteByUserService $clientDeleteById): JsonResponse
    {
        try {
            $user = $this->authChecker->getAuthenticated($security);
            $clientDeleteById($user);

            return new JsonResponse(["message" => "Client deleted successfully"], 200);

        } catch (UserNotFoundException $e) {
            return new JsonResponse(["message" => $e->getMessage()], 404);
        } catch (\DomainException $e) {
            return new JsonResponse(["message" => $e->getMessage()], 402);
        } catch (\Exception $e) {
            return new JsonResponse(["message" => $e->getMessage()], 500);
        }
    }


}