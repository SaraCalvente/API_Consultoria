<?php

namespace App\User\Application;

use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminService
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;


    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function registerAdmin(string $email, string $password): JsonResponse
    {
        $user = new User();
        $user->setEmail(new EmailValueObject($email));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'User registered successfully',
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ], 201);
    }

    public function getAdminUsers(): JsonResponse
    {
        $admins = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()
            ->getResult();

        $adminsData = [];
        foreach ($admins as $admin) {
            $adminsData[] = [
                'user_id' => $admin->getId(),
                'email' => $admin->getEmail(),
                'roles' => $admin->getRoles(),
            ];
        }

        return new JsonResponse($adminsData, 200);
    }


    public function deleteAdmin(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if(!$user){
            return new JsonResponse(['error' => 'Admin not found'], 404);

        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new JsonResponse([
            'message' => 'Admin deleted successfully',
        ], 200);

    }

}