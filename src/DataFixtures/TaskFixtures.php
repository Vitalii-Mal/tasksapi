<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Enum\StatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            // Get a link to the user
            $user = $this->getReference('user_' . $i);

            // Create 5 tasks for each user
            for ($j = 1; $j <= 5; $j++) {
                $task = new Task();
                $task->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph())
                    ->setStatus($faker->randomElement([StatusEnum::TODO->value, StatusEnum::DONE->value]))
                    ->setPriority($faker->numberBetween(1, 5))
                    ->setCreatedAt($faker->dateTimeThisYear)
                    ->setUser($user);

                if ($task->getStatus() === StatusEnum::DONE->value) {
                    $task->setCompletedAt($faker->dateTimeThisYear);
                }

                $manager->persist($task);

                // Create 2 subtasks for each task
                for ($k = 1; $k <= 2; $k++) {
                    $subtask = new Task();

                    if ($task->getStatus() === StatusEnum::DONE->value) {
                        $subtaskStatus = StatusEnum::DONE->value;
                    } else {
                        $subtaskStatus = $faker->randomElement([StatusEnum::TODO->value, StatusEnum::DONE->value]);
                    }

                    $subtask->setTitle($faker->sentence())
                        ->setDescription($faker->paragraph())
                        ->setStatus($subtaskStatus)
                        ->setPriority($faker->numberBetween(1, 5))
                        ->setCreatedAt($faker->dateTimeThisYear)
                        ->setUser($user)
                        ->setParentTask($task);

                    if ($subtask->getStatus() === StatusEnum::DONE->value) {
                        $subtask->setCompletedAt($faker->dateTimeThisYear);
                    }

                    $manager->persist($subtask);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
