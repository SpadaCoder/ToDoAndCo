<?php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\User;
use App\Entity\Task;

class UserEntityTest extends TestCase
{
    private $user;

    /**
     * Initialisation d'une instance de User avant chaque test.
     */
    public function setUp(): void
    {
        $this->user = new User();
    }

    // ------------------------------
    // Tests sur les propriétés simples
    // ------------------------------

    /**
     * Teste si l'ID est null par défaut.
     */
    public function testId(): void
    {
        $this->assertNull($this->user->getId());
    }

    /**
     * Teste si le nom de l'utilisateur est correctement défini.
     */
    public function testName(): void
    {
        $this->user->setUsername('test');
        $this->assertSame('test', $this->user->getUsername());
    }

    /**
     * Teste si le mot de passe de l'utilisateur est correctement défini.
     */
    public function testPassword(): void
    {
        $this->user->setPassword('pwd');
        $this->assertSame('pwd', $this->user->getPassword());
    }

    /**
     * Teste si l'email de l'utilisateur est correctement défini.
     */
    public function testEmail(): void
    {
        $this->user->setEmail('test@test.com');
        $this->assertSame('test@test.com', $this->user->getEmail());
    }

    // ------------------------------
    // Tests sur les rôles de l'utilisateur
    // ------------------------------

    /**
     * Teste si les rôles de l'utilisateur sont correctement définis.
     */
    public function testRoles(): void
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    // ------------------------------
    // Tests sur la sécurité de l'utilisateur
    // ------------------------------

    /**
     * Teste si la méthode getSalt retourne null.
     */
    public function testSalt(): void
    {
        $this->assertNull($this->user->getSalt());
    }

    /**
     * Teste si la méthode eraseCredentials ne fait rien.
     */
    public function testEraseCredentials(): void
    {
        $this->assertNull($this->user->eraseCredentials());
    }

    // ------------------------------
    // Tests sur l'interface UserInterface
    // ------------------------------

    /**
     * Teste si le nom d'utilisateur est correctement défini.
     */
    public function testUsername(): void
    {
        $this->user->setUsername('userTest');
        $this->assertSame('userTest', $this->user->getUsername());
    }

    // ------------------------------
    // Tests sur les propriétés complexes
    // ------------------------------

    /**
     * Test de l'ajout d'une tâche à un utilisateur.
     */
    public function testAddTask()
    {
        $user = new User();
        $task = new Task();

        $user->addTask($task);
        $this->assertCount(1, $user->getTasks());
        $this->assertTrue($user->getTasks()->contains($task));
        $this->assertSame($user, $task->getUser());
    }

    /**
     * Test de la suppression d'une tâche d'un utilisateur.
     */
    public function testRemoveTask()
    {
            $user = new User();
            $task = new Task();
        
            // Ajout d'une tâche à la collection de l'utilisateur
            $user->addTask($task);
        
            // Vérification que la tâche a bien été ajoutée
            static::assertCount(1, $user->getTasks());
        
            // Suppression de la tâche
            $user->removeTask($task);
        
            // Vérification que la collection est maintenant vide
            static::assertEmpty($user->getTasks());
    }

    /**
     * Test de la suppression d'une tâche qui n'existe pas dans la collection d'un utilisateur.
     */
    public function testRemoveTaskWhenTaskNotInCollection()
    {
        $user = new User();

        // Tentative de suppression d'une tâche qui n'est pas dans la collection
        $task = new Task();
        static::assertInstanceOf(User::class, $user->removeTask($task));

        static::assertEmpty($user->getTasks());
    }

    // ------------------------------
    // Nettoyage des données
    // ------------------------------
    public function tearDown(): void
    {
        $this->user = null;
    }
}
