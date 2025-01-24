<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SecurityControllerTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $entityManager;

    /**
     * Initialisation d'une instance du client avant chaque test.
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        
    }

    /**
     * Nettoyage après chaque test.
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    // ------------------------------
    // Tests sur la page de login
    // ------------------------------

    /**
     * Teste si la page de login est accessible.
     */
    public function testLoginPageIsAccessible(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste que la page de login contient le bon titre.
     */
    public function testLoginPageTitle(): void
    {
        $this->client->request('GET', '/login');
        $this->assertSelectorTextContains('title', 'Login');
    }

    /**
     * Teste si le formulaire de connexion est affiché.
     */
    public function testLoginFormIsDisplayed(): void
    {
        $this->client->request('GET', '/login');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="username"]');
        $this->assertSelectorExists('input[name="password"]');
    }

    // ------------------------------
    // Tests sur la soumission du formulaire
    // ------------------------------

    /**
     * Teste que la soumission d'un mauvais formulaire retourne une erreur.
     */
    public function testInvalidLogin(): void
    {
        $this->client->request('POST', '/login', [
            'username' => 'invalid_user',
            'password' => 'invalid_password',
        ]);
        $this->assertResponseRedirects();
        $this->assertSelectorTextContains('.flash-error', 'Invalid credentials');
    }

    /**
     * Teste que la soumission d'un formulaire valide redirige vers la page d'accueil.
     */
    public function testValidLogin(): void
    {
        $user = new User();
        $user->setUsername('test_user');
        $user->setEmail('testuser@example.com');
        $user->setPassword('password123');
        $user->setRoles(['ROLE_USER']); 
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->request('POST', '/login', [
            'username' => 'admin',
            'password' => 'admin_password',
        ]);
        $this->assertResponseRedirects('/');
    }

    // ------------------------------
    // Tests sur la déconnexion
    // ------------------------------

    /**
     * Teste que la déconnexion fonctionne correctement.
     */
    public function testLogout(): void
    {
        // Simulez un utilisateur connecté
        $this->client->request('POST', '/login', [
            'username' => 'admin',
            'password' => 'admin_password',
        ]);

        // Déconnexion
        $this->client->request('GET', '/logout');
        $this->assertResponseRedirects('/');
        $this->assertSelectorTextContains('.flash-success', 'You have been logged out');
    }

    /**
     * Teste l'accès à une page protégée sans être connecté.
     */
    public function testAccessProtectedPageWithoutLogin(): void
    {
        $this->client->request('GET', 'admin/users/create');
        $this->assertResponseRedirects('/login');
    }

    /**
     * Teste que l'utilisateur peut accéder à la page protégée après une connexion.
     */
    public function testAccessProtectedPageWithLogin(): void
    {
        // Créez un utilisateur valide et connectez-le
        $this->client->request('POST', '/login', [
            'username' => 'adminUser',
            'password' => 'adminUser_password',
        ]);

        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
    }
}
