<?php

namespace App\Tests\api\TestDataFixtures;

use App\User\Domain\Notification;
use App\User\Domain\User;
use App\User\Domain\ValueObject\EmailValueObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getNotificationsData() as [$creatorEmail, $message, $users]) {
            $notification = new Notification();
            $notification->setMessage($message);
            $notification->setDate(new \DateTime());
            $user = $manager->getRepository(User::class)->findOneBy(['email' => new EmailValueObject($creatorEmail)]);
            $notification->setCreatorUser($user);

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
            ['user@example.com','New update available', [1, 2]],
            ['ana@garcia.com', 'Reminder: Project deadline approaching', [2, 3]],
            ['user@example.com','Your task has been approved', [1]],
        ];
    }
}
