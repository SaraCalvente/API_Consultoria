<?php

namespace App\Consultant\Application\Consultant;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByEmailService
{
    public function __construct(private ConsultantRepositoryInterface $consultantRepository, private ProjectRepositoryInterface $projectRepository, private UserRepositoryInterface       $userRepository)
    {
    }

    public function __invoke(array $data): JsonResponse
    {
        $user = $this->userRepository->findUserByEmailOrFail($data['email']);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $projects = $this->projectRepository->checkIfConsultantHasProjects($consultant);
        if (!$projects instanceof \Symfony\Component\HttpFoundation\JsonResponse){
            return $this->consultantRepository->deleteConsultant($consultant);
        }
        return $projects;
    }
}