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
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getProjectData() as [$name, $description, $startDate, $endDate, $status, $clientEmail, $consultantEmails]) {
            // Buscar el cliente en la base de datos
            #$client = $manager->getRepository(Client::class)->findOneBy(['email' => $clientEmail]);


            $project = new Project();
            $project->setName($name);
            $project->setDescription($description);
            $project->setStartDate(new \DateTimeImmutable($startDate));
            $project->setEndDate($endDate ? new \DateTimeImmutable($endDate) : null);
            $project->setStatus($status);
            #$project->setClient($client);

            // Asignar consultores al proyecto
            foreach ($consultantEmails as $email) {
                $consultant = $manager->getRepository(Consultant::class)->findOneBy(['user' => $email]);

                if (!$consultant) {
                    throw new \Exception("El consultor con email $email no fue encontrado en la base de datos.");
                }

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
                'user@example.com',
                [1, 2]
            ],
            [
                'Project B',
                'Mobile app for healthcare',
                '2024-03-15',
                null,
                Status::COMPLETADO,
                'admin@example.com',
                ['user@example.com']
            ],
            [
                'Project C',
                'E-commerce website',
                '2024-02-10',
                '2024-08-10',
                Status::PENDIENTE,
                'sara@calvente.es',
                ['sara@calvente.es', 'user@example.com']
            ],
        ];
    }
}

