<?php

namespace App\DataFixtures;

use App\Client\Domain\Client;
use App\Consultant\Domain\Consultant;
use App\Project\Domain\Project;
use App\Project\Domain\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws \DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getProjectData() as [$name, $description, $startDate, $endDate, $status, $clientId, $consultantIds]) {
            $client = $manager->getRepository(Client::class)->findOneBy(['id' => $clientId]);

            $project = new Project();
            $project->setName($name);
            $project->setDescription($description);
            $project->setStartDate(new \DateTimeImmutable($startDate));
            $project->setEndDate($endDate ? new \DateTimeImmutable($endDate) : null);
            $project->setStatus($status);
            $project->setClient($client);

            foreach ($consultantIds as $id) {

                $consultant = $manager->getRepository(Consultant::class)->findOneBy(['id' => $id]);

                $project->addConsultant($consultant);
            }

            $manager->persist($project);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ConsultantFixtures::class,
            ClientFixtures::class,
        ];
    }

    protected function getProjectData(): array
    {
        return [
            [
                'Project A',
                'Development of a web application',
                '2024-01-01',
                '2024-06-30',
                Status::EN_PROCESO,
                1,
                [1, 2]
            ],
            [
                'Project B',
                'Mobile app for healthcare',
                '2024-03-15',
                null,
                Status::COMPLETADO,
                2,
                [3]
            ],
            [
                'Project C',
                'E-commerce website',
                '2024-02-10',
                '2024-08-10',
                Status::PENDIENTE,
                2,
                [1, 3]
            ],
        ];
    }
}

