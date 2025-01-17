<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Créer le user "anonyme"
        $anonymousUser = new User();
        $anonymousUser->setEmail('anonyme@example.com');
        $anonymousUser->setUsername('anonyme');
        $anonymousUser->setPassword($this->passwordHasher->hashPassword($anonymousUser, 'password123'));
        $manager->persist($anonymousUser);

        // Créer d'autres utilisateurs
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setUsername($faker->userName());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $manager->persist($user);
        }

        // Créer des tâches pour le user "anonyme"
        for ($k = 0; $k < 5; $k++) {
            $task = new Task();
            $task->setTitle('Tâche par défaut ' . ($k + 1));
            $task->setContent('Ceci est une tâche par défaut associée à l’utilisateur anonyme.');
            $task->setUser($anonymousUser);
            $manager->persist($task);
        }

        // Sauvegarder tout dans la base de données
        $manager->flush();
    }
}
