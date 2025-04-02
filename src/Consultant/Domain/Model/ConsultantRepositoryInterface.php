<?php

namespace App\Consultant\Domain\Model;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ConsultantRepositoryInterface
{
    public function findConsultantByUser(User $user): ?Consultant;
    public function checkIfConsultantExists(User $user): bool;
    public function findAllConsultants(): array;
    public function updateConsultant(User $user, ?string $profile): JsonResponse;
    public function deleteConsultant(Consultant $consultant): JsonResponse;
    public function addConsultant(Consultant $consultant): void;
    public function saveConsultant(): void;
    public function removeConsultant(Consultant $consultant): void;

}