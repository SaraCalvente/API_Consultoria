<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\Admin\ConsultantDeleteByEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantAdminDeleteController extends AbstractController
{
    #[Route('/admin/consultant/delete', name: 'admin_delete_consultant', methods: ['DELETE'])]
    public function adminDeleteConsultant(Request $request, ConsultantDeleteByEmailService $consultantDeleteByEmailService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            return $consultantDeleteByEmailService($data['email']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}