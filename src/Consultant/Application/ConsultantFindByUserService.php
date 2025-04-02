<?php
namespace App\Consultant\Application;

use App\Consultant\Domain\ConsultantDTO;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantFindByUserService
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