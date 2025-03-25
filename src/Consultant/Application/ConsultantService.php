<?php
// src/Consultant/Application/ConsultantService.php

namespace App\Consultant\Application;

use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Profile;
use App\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConsultantService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createConsultantProfile(User $user, array $data): void
    {
        $consultant = new Consultant();
        $consultant->setUserId($user);
        $consultant->setName($data['name']);
        $consultant->setSurnames($data['surnames']);
        $profile = Profile::from($data["profile"]);
        $consultant->setProfile($profile);

        $this->entityManager->persist($consultant);
    }


    public function getConsultantById(int $id): array
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->find($id);

        if (!$consultant) {
            throw new \Exception('Consultant not found');
        }

        return [
            'consultant_id' => $consultant->getId(),
            'name' => $consultant->getName(),
            'surnames' => $consultant->getSurnames(),
            'profile' => $consultant->getProfile(),
        ];
    }

    public function getAllConsultants(): JsonResponse
    {
        $consultants = $this->entityManager->getRepository(Consultant::class)->findAll();

        $consultantData = [];
        foreach ($consultants as $consultant) {
            $consultantData[] = [
                'consultant_id' => $consultant->getId(),
                'name' => $consultant->getName(),
                'surnames' => $consultant->getSurnames(),
                'profile' => $consultant->getProfile(),
            ];
        }

        return new JsonResponse($consultantData, 200);
    }

    public function deleteConsultant(int $id): void
    {
        $consultant = $this->entityManager->getRepository(Consultant::class)->find($id);

        if (!$consultant) {
            throw new \Exception('Consultant not found');
        }

        $this->entityManager->remove($consultant);
        $this->entityManager->flush();
    }

}
