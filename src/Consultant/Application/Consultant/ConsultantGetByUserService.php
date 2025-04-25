<?php
namespace App\Consultant\Application\Consultant;

use App\Consultant\Domain\Consultant\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantGetByUserService
{
    private ConsultantRepositoryInterface $consultantRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        return new JsonResponse(ConsultantDTO::fromEntity($consultant));
    }
}