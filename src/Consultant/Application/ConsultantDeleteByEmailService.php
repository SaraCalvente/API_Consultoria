<?php

namespace App\Consultant\Application;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByEmailService
{
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        return $this->consultantRepository->removeConsultant(['email' => $email]);
    }
}