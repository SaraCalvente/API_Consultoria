<?php
declare(strict_types=1);

namespace App\Consultant\Application\Ability;

use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbilityGetByConsultantEmailService
{
    private AbilityRepositoryInterface $abilityRepository;
    private ConsultantRepositoryInterface $consultantRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(
        AbilityRepositoryInterface    $abilityRepository,
        ConsultantRepositoryInterface $consultantRepository, UserRepositoryInterface $userRepository
    )
    {
        $this->abilityRepository = $abilityRepository;
        $this->consultantRepository = $consultantRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $email): JsonResponse
    {
        $user = $this->userRepository->findUserByEmail($email);

        if(!$this->consultantRepository->checkIfConsultantExists($user)) {
            return new JsonResponse(['error' => 'User ' . $user->getEmail() . ' is not a Consultant'], 404);
        }
        $consultant = $this->consultantRepository->findConsultantByUser($user);
        $abilities = $this->abilityRepository->findAbilitiesByConsultant($consultant);

        return new JsonResponse([
            'message' => 'Ability successfully retrieved.',
            'consultant_email' => $user->getEmail(),
            'consultant_name' => $consultant->getName(),
            'consultant_id' => $consultant->getId(),
            'abilities' => array_map(fn($c) => ['name' => $c->getName(), 'level' => $c->getLevel()->value], $abilities),

        ], 201);
    }
}