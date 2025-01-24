<?php

namespace App\tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Task;
use App\Entity\User;

class TaskEntityTest extends TestCase
{
    private $task;

    /**
     * Initialisation d'une instance de Task avant chaque test.
     */
    public function setUp(): void
    {
        $this->task = new Task();
    }

    // ------------------------------
    // Tests sur les propriétés simples
    // ------------------------------

    /**
     * Teste si l'ID est null par défaut.
     */
    public function testId(): void
    {
        $this->assertNull($this->task->getId());
    }

    /**
     * Teste le titre de la tâche.
     */
    public function testTitle(): void
    {
        $this->task->setTitle('Test Title');
        $this->assertSame('Test Title', $this->task->getTitle());
    }

    /**
     * Teste le contenu de la tâche.
     */
    public function testContent(): void
    {
        $this->task->setContent('Lorem ipsum');
        $this->assertSame('Lorem ipsum', $this->task->getContent());
    }

    /**
     * Teste l'état de la tâche (faite ou non).
     */
    public function testIsDone(): void
    {
        $this->task->setIsDone(true);
        $this->assertTrue($this->task->isDone());

        $this->task->setIsDone(false);
        $this->assertFalse($this->task->isDone());
    }

    // ------------------------------
    // Tests sur les propriétés complexes
    // ------------------------------

    /**
     * Teste la date de création de la tâche.
     */
    public function testCreatedAt(): void
    {
        $date = new \DateTime();
        $this->task->setCreatedAt($date);
        $this->assertSame($date, $this->task->getCreatedAt());
    }

    /**
     * Teste l'utilisateur associé à la tâche.
     */
    public function testUser(): void
    {
        $user = new User();
        $this->task->setUser($user);
        $this->assertInstanceOf(User::class, $this->task->getUser());
    }

    // ------------------------------
    // Tests avancés avec data providers
    // ------------------------------

    /**
     * Teste différents titres en utilisant un data provider.
     *
     * @dataProvider titleProvider
     */
    public function testTitleWithDataProvider(string $title): void
    {
        $this->task->setTitle($title);
        $this->assertSame($title, $this->task->getTitle());
    }

    /**
     * Fournit des titres pour le test data provider.
     */
    public function titleProvider(): array
    {
        return [
            ['First Title'],
            ['Another Example'],
            ['Special Characters !@#$%^'],
            [''],
        ];
    }

    // ------------------------------
    // Tests des cas limites et erreurs
    // ------------------------------

    /**
     * Teste le titre avec une chaîne vide.
     */
    public function testEmptyTitle(): void
    {
        $this->task->setTitle('');
        $this->assertSame('', $this->task->getTitle());
    }

    /**
     * Teste l'utilisateur avec une valeur invalide (TypeError attendu).
     */
    public function testInvalidUserThrowsError(): void
    {
        $this->expectException(\TypeError::class);
        $this->task->setUser('Invalid User'); // Doit provoquer une erreur.
    }

    // ------------------------------
    // Tests avec des mocks
    // ------------------------------

    /**
     * Teste l'attribution d'un utilisateur via un mock.
     */
    public function testMockedUser(): void
    {
        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('getUsername')
            ->willReturn('MockedUser');

        $this->task->setUser($userMock);

        $this->assertSame($userMock, $this->task->getUser());
        $this->assertEquals('MockedUser', $this->task->getUser()->getUsername());
    }
    // ------------------------------
    // Nettoyage des données
    // ------------------------------
    public function tearDown(): void
    {
        $this->task = null;
    }
}
