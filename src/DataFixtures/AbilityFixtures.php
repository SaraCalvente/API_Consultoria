<?php

namespace App\DataFixtures;

use App\Consultant\Domain\Ability;
use App\Consultant\Domain\Consultant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AbilityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getAbilitiesData() as [$name, $consultants]) {
            $ability = new Ability();
            $ability->setName($name);

            foreach ($consultants as $id) {
                $consultant = $manager->getRepository(Consultant::class)->findOneBy(['id' => $id]);

                if ($consultant) {
                    $ability->addConsultant($consultant);
                }
                $manager->persist($ability);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ConsultantFixtures::class,
        );
    }

    protected function getAbilitiesData(): array
    {
        return [
            ['PHP', [2]],
            ['JavaScript', [1, 3]],
            ['Java', [3, 1]],
        ];
    }
}