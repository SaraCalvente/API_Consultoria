<?php

namespace App\Consultant\Application\Consultant\Admin;

use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantGetAllService
{
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
    }

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