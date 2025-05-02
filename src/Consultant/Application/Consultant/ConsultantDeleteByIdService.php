<?php

namespace App\Consultant\Application\Consultant;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class ConsultantDeleteByIdService
{
    public function __construct(
        private ConsultantRepositoryInterface $consultantRepository,
        private ProjectRepositoryInterface $projectRepository
    )
    {}

    public function __invoke(User $user): JsonResponse
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $projects = $this->projectRepository->checkIfConsultantHasProjects($consultant);
        if (!$projects instanceof \Symfony\Component\HttpFoundation\JsonResponse){
            return $this->consultantRepository->deleteConsultant($consultant);
        }
        return $projects;
    }

}