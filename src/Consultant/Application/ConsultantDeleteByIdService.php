<?php

namespace App\Consultant\Application;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByIdService
{
    private ConsultantRepositoryInterface $consultantRepository;
    private ProjectRepositoryInterface $projectRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository,
        ProjectRepositoryInterface $projectRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $projects = $this->projectRepository->checkIfConsultantHasProjects($consultant);
        if(!$projects){
            return $this->consultantRepository->deleteConsultant($consultant);
        }
        return $projects;
    }

}