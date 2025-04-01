<?php
namespace App\Consultant\Application;

use App\Client\Domain\Model\ClientRepositoryInterface;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Shared\Domain\Exception\ConsultantNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantFindByIdService
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
        $consultant = $this->consultantRepository->findConsultantById($userId);
        return new JsonResponse(ConsultantDTO::fromEntity($consultant));
    }
}