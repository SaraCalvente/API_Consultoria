<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\Admin\ConsultantUpdateByEmailService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantAdminUpdateController extends AbstractController
{
    #[Route('/admin/consultant/update', name: 'admin_consultant_update', methods: ['PUT'])]
    public function adminUpdateConsultant(Request $request, ConsultantUpdateByEmailService $consultantUpdateByEmail): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        return $consultantUpdateByEmail(
            $data['email'] ?? null,
            $data['profile'] ?? null
        );
    }

}