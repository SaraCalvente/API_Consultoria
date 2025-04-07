<?php

namespace App\Consultant\Application\Admin;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\Project\Domain\Model\ProjectRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByEmailService
{
    private ConsultantRepositoryInterface $consultantRepository;
    private ProjectRepositoryInterface $projectRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository,
        ProjectRepositoryInterface $projectRepository,
        UserRepositoryInterface       $userRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $projects = $this->projectRepository->checkIfConsultantHasProjects($consultant);
        if(!$projects){
            return $this->consultantRepository->deleteConsultant($consultant);
        }
        return $projects;
    }
}