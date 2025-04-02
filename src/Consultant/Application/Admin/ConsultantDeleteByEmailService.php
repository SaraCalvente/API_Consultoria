<?php

namespace App\Consultant\Application\Admin;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantDeleteByEmailService
{
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        ConsultantRepositoryInterface $consultantRepository,
        UserRepositoryInterface       $userRepository
    )
    {
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);
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