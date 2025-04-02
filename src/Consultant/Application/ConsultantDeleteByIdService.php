<?php

namespace App\Consultant\Application;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
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
        ProjectRepositoryInterface    $projectRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(User $user): JsonResponse
    {
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $projects = $consultant->getProject()->toArray();

        if (count($projects) > 0) {
            $projectDetails = array_map(fn($p) => ['id' => $p->getId(), 'name' => $p->getName()], $projects);

            return new JsonResponse([
                'error' => 'Cannot delete consultant because there are associated projects.',
                'projects' => $projectDetails
            ], 400);
        }
        return $this->consultantRepository->deleteConsultant($consultant);
    }

}