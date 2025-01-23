<?php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\User;

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

}
