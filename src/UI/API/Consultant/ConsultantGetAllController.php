<?php
declare(strict_types=1);

namespace App\UI\API\Consultant;

use App\Consultant\Application\Admin\ConsultantFindAllService;
use App\Shared\Domain\Auth\AuthChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConsultantGetAllController extends AbstractController
{
    #[Route('/admin/consultants', name: 'get_all_consultants', methods: ['GET'])]
    public function getAllConsultants(ConsultantFindAllService $consultantFindAllService): JsonResponse
    {
        return $consultantFindAllService();
    }


}