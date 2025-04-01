<?php

namespace App\DataFixtures;

use App\Consultant\Domain\Consultant;
use App\Project\Domain\Project;
use App\Project\Domain\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws \DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getTaskData() as [$projectId, $name, $description, $status, $startDateStr, $endDateStr, $consultantIds]) {
            $project = $manager->getRepository(Project::class)->find($projectId);

            if (!$project) {
                throw new \Exception("El proyecto con ID $projectId no fue encontrado en la base de datos.");
            }

            $startDate = new \DateTime($startDateStr);
            $endDate = new \DateTime($endDateStr);

            $task = new Task();
            $task->setProject($project);
            $task->setName($name);
            $task->setDescription($description);
            $task->setStatus($status);
            $task->setStartDate($startDate);
            $task->setEndDate($endDate);

            foreach ($consultantIds as $id) {
                $consultant = $manager->getRepository(Consultant::class)->findOneBy(['id' => $id]);
                $project->addConsultant($consultant);
            }

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProjectFixtures::class,
        ];
    }

    protected function getTaskData(): array
    {
        return [
            [1, 'Definir requisitos', 'Analizar necesidades del cliente', 'Pendiente', '2025-04-01 08:00:00', '2025-04-01 10:30:00', [1, 2]],
            [2, 'Diseño de arquitectura', 'Estructurar la aplicación', 'En progreso', '2025-04-02 09:00:00', '2025-04-02 14:00:00', [3]],
            [3, 'Implementación API', 'Desarrollar endpoints', 'Pendiente', '2025-04-03 08:00:00', '2025-04-05 16:45:00', [2]],
        ];
    }
}

