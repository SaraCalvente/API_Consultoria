<?php

namespace App\DataFixtures;

use App\Client\Domain\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getClientData() as [$name, $surnames, $address]) {
            $client = new Client();
            $client->setName($name);
            $client->setSurnames($surnames);
            $client->setAddress($address);

            $manager->persist($client);
        }

        $manager->flush();
    }

    protected function getClientData(): array
    {
        return [
            ['Alex', 'Gonzalez', '123 Main Street'],
            ['Ana', 'García García', '456 Elm Street'],
            ['Miguel', 'Piquer Moli', '789 Oak Avenue'],
        ];
    }
}
