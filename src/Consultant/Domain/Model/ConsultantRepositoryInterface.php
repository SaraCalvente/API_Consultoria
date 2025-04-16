<?php

namespace App\Consultant\Domain\Model;

use App\Consultant\Domain\Consultant\Consultant;
use App\User\Domain\User;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ConsultantRepositoryInterface
{
    public function findConsultantByUser(User $user): ?Consultant;
    public function checkIfConsultantExists(User $user): bool;
    public function findAllConsultants(): array;
    public function deleteConsultant(Consultant $consultant): JsonResponse;
    public function addConsultant(Consultant $consultant): void;
    public function saveConsultant(): void;
    public function removeConsultant(Consultant $consultant): void;
    public function getConsultantByUser(User $user): Consultant;


}