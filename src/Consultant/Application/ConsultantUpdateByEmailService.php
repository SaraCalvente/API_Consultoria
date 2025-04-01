<?php

namespace App\Consultant\Application;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantUpdateByEmailService
{
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke( string $email, ?string $profile = null
    ): JsonResponse {
        return $this->consultantRepository->modifyConsultant(['email' => $email], $profile);

    }

}