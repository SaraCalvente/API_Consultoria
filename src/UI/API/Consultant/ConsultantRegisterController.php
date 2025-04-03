<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\ConsultantRegisterService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantRegisterController extends AbstractController
{

    #[Route('/register/consultant', name: 'consultant_register', methods: ['POST'])]
    public function register(
        Request $request, ConsultantRegisterService $consultantRegisterService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        return $consultantRegisterService(
            $data['email'],
            $data['password'],
            $data['name'],
            $data['surnames'],
            $data['profile']);
    }

}