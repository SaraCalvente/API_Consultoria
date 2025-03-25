<?php

namespace App\DataFixtures;

use App\Consultant\Domain\Ability;
use App\Consultant\Domain\Consultant;
use App\Consultant\Domain\Profile;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ConsultantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        foreach ($this->getConsultantData() as [$name, $surname, $email, $profile]) {
            $user = $manager->getRepository(User::class)->findOneBy(['email' => new EmailValueObject($email)]);

            if (!$user) {
                throw new \Exception("El usuario con email $email no fue encontrado en la base de datos.");
            }

            $consultant = new Consultant();
            $consultant->setName($name);
            $consultant->setSurnames($surname);
            $consultant->setUserId($user);
            $consultant->setProfile($profile);

            $manager->persist($consultant);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }

    protected function getConsultantData(): array{
        return [
            ['John', 'Doe', new EmailValueObject('user@example.com'), Profile::DESARROLLADOR],
            ['Sara', 'Calvente', new EmailValueObject('sara@calvente.es'), Profile::LIDER_TECNICO],
            ['Ana', 'Prueba', new EmailValueObject('admin@example.com'), Profile::PROJECT_MANAGER],

        ];
    }
}

