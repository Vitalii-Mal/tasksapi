<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername('user' . $i);
            $user->setPassword($this->passwordHasher->hashPassword($user, '1'));
            $manager->persist($user);

            // Save the user as a reference for use in TaskFixtures
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
