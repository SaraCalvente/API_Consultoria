<?php

namespace App\Consultant\Application;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantUpdateByIdService
{
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke( User $user, ?string $profile = null
    ): JsonResponse {
        return $this->consultantRepository->updateConsultant($user, $profile);
    }
}