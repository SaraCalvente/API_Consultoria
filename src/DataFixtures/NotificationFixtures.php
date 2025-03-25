<?php

namespace App\DataFixtures;

use App\User\Domain\Notification;
use App\User\Domain\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getNotificationsData() as [$message, $users]) {
            $notification = new Notification();
            $notification->setMessage($message);
            $notification->setDate(new \DateTime());

            foreach ($users as $userId) {
                $user = $manager->getRepository(User::class)->findOneBy(['id' => $userId]);

                if ($user) {
                    $notification->addUser($user);
                }
            }

            $manager->persist($notification);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }

    protected function getNotificationsData(): array
    {
        return [
            ['New update available', [1, 2]],
            ['Reminder: Project deadline approaching', [2, 3]],
            ['Your task has been approved', [1]],
        ];
    }
}
