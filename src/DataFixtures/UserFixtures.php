<?php

namespace App\DataFixtures;


use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $password_encoder)
    {
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$email, $password, $roles])
        {
            $user = new User();
            $user->setEmail(new EmailValueObject($email));
            $user->setPassword($this->password_encoder->hashPassword($user, $password));
            $user->setRoles($roles);

            $manager->persist($user);
        }
        $manager->flush();
    }

    private function getUserData(): array
    {
        return [

            ['user@example.com', 'passw', ['ROLE_CONSULTANT']],
            ['admin@example.com', 'passw', ['ROLE_ADMIN']],
            ['prueba@example.com', 'passw', ['ROLE_CONSULTANT']],
            ['sara@calvente.es', 'passw', ['ROLE_CONSULTANT']],
            ['alex@gonzalez.com','passw', ['ROLE_CLIENT']],
            ['ana@garcia.com', 'passw', ['ROLE_CLIENT']],
            ['miguel@piquer.com', 'passw', ['ROLE_CLIENT']],


        ];
    }
}

