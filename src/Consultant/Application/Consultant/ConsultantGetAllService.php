<?php

namespace App\Consultant\Application\Consultant;

use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ConsultantGetAllService
{
    public function __construct(
        private ConsultantRepositoryInterface $consultantRepository
    )
    {}

    public function __invoke(): JsonResponse
    {
        $consultants = $this->consultantRepository->findAllConsultants();

        $consultantData = [];
        foreach ($consultants as $consultant) {
            $consultantData[] = ConsultantDTO::fromEntity($consultant);
        }

        return new JsonResponse($consultantData, 200);
    }
}