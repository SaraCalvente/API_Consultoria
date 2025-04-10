<?php

namespace App\DataFixtures;

use App\Client\Domain\Client;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getClientData() as [$name, $surnames, $address, $email, $phoneNumber]) {
            $user = $manager->getRepository(User::class)->findOneBy(['email' => new EmailValueObject($email)]);

            if (!$user) {
                throw new \Exception("El usuario con email $email no fue encontrado en la base de datos.");
            }
            $client = new Client();
            $client->setName($name);
            $client->setSurnames($surnames);
            $client->setAddress($address);
            $client->setPhoneNumber($phoneNumber);
            $client->setUser($user);

            $manager->persist($client);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }

    protected function getClientData(): array
    {
        return [
            ['Alex', 'Gonzalez', '123 Main Street', 'alex@gonzalez.com', '664 455 223'],
            ['Ana', 'García García', '456 Elm Street', 'ana@garcia.com', '666 666 666'],
            ['Miguel', 'Piquer Moli', '789 Oak Avenue', 'miguel@piquer.com', '555 555 555'],
        ];
    }
}
