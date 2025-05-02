<?php
declare(strict_types=1);

namespace App\Consultant\Application\Ability;

use App\Consultant\Domain\Model\AbilityRepositoryInterface;
use App\Consultant\Domain\Model\ConsultantRepositoryInterface;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class AbilityGetByConsultantService
{
    public function __construct(
        private AbilityRepositoryInterface $abilityRepository,
        private ConsultantRepositoryInterface $consultantRepository
    )
    {}

    public function __invoke(User $user): JsonResponse
    {
        if (!$this->consultantRepository->checkIfConsultantExists($user)) {
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