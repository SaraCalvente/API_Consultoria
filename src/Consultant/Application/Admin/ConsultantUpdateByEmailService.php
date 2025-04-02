<?php

namespace App\Consultant\Application\Admin;

use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantUpdateByEmailService
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

    public function __invoke( string $email, ?string $profile = null
    ): JsonResponse {
        $user = $this->userRepository->findUserByEmail($email);
        return $this->consultantRepository->updateConsultant($user, $profile);

    }

}