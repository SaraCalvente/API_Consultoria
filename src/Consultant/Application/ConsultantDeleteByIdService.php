<?php

namespace App\Consultant\Application;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByIdService
{
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(int $userId): JsonResponse
    {
        return $this->consultantRepository->removeConsultant(['id' => $userId]);
    }

}