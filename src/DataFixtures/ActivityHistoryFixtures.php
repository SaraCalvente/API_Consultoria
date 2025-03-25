<?php

namespace App\DataFixtures;

use App\ActivityHistory\Domain\ActivityHistory;
use App\Project\Domain\Project;
use App\User\Domain\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActivityHistoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getActivityHistoryData() as [$description, $userEmail, $projectId]) {
            $activityHistory = new ActivityHistory();
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
            ['Project created', 'user@example.com', 1],
            ['Requirements phase started', 'admin@example.com', 1],
            ['Deployment completed', 'sara@calvente.es', 2],
            ['Design review completed', 'user@example.com', 3],
        ];
    }
}
