<?php
declare(strict_types=1);

namespace App\UI\API\User;

use App\User\Application\UserFindAllService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserGetAllController extends AbstractController
{
    #[Route('/users', name: 'get_all_users', methods: ['GET'])]
    public function getUsers(UserFindAllService $userFindAllService): JsonResponse
    {
        return $userFindAllService();
    }

}