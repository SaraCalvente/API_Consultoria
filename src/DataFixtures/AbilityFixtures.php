<?php

namespace App\DataFixtures;

use App\Consultant\Domain\Ability\Ability;
use App\Consultant\Domain\Ability\Level;
use App\Consultant\Domain\Consultant\Consultant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AbilityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getAbilitiesData() as [$name, $level, $consultants]) {
            $ability = new Ability();
            $ability->setName($name);
            $ability->setLevel(Level::from($level));

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
            ['PHP', 'Alto', [2]],
            ['JavaScript', 'Bajo', [1, 3]],
            ['Java', 'Experto', [3, 1]],
        ];
    }
}