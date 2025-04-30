<?php

namespace App\Tests\api\TestDataFixtures;

use App\ActivityHistory\Domain\ActivityHistory;
use App\Project\Domain\Project\Project;
use App\User\Domain\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActivityHistoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getActivityHistoryData() as [$name, $description, $userEmail, $projectId]) {
            $activityHistory = new ActivityHistory();
            $activityHistory->setName($name);
            $activityHistory->setDate(new \DateTimeImmutable());
            $activityHistory->setDescription($description);

            $user = $manager->getRepository(User::class)->findOneBy(['email' => $userEmail]);
            $activityHistory->setUser($user);

            $project = $manager->find(Project::class, $projectId);
            $activityHistory->setProject($project);

            $manager->persist($activityHistory);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }

    protected function getActivityHistoryData(): array
    {
        return [
            ['Act 1','Project created', 'user@example.com', 1],
            ['Act 2','Requirements phase started', 'admin@example.com', 1],
            ['Act 3','Deployment completed', 'sara@calvente.es', 2],
            ['Act 4','Design review completed', 'sara@calvente.es', 3],
        ];
    }
}
