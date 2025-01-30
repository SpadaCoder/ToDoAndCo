<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class SecurityControllerTest extends WebTestCase
{
    private $client;

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
     * Teste si le formulaire de connexion est affiché.
     */
    public function testLoginFormIsDisplayed(): void
    {
        $this->client->request('GET', '/login');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
    }

    // ------------------------------
    // Tests sur la soumission du formulaire
    // ------------------------------

    /**
     * Teste que la soumission d'un mauvais formulaire retourne une erreur.
     */
    public function testInvalidLogin(): void
    {        
        $crawler = $this->client->request('GET', '/login');
        
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'invalid_user',
            '_password' => 'invalid_password'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials');
    }

    /**
     * Teste que la soumission d'un formulaire valide redirige vers la page d'accueil.
     */
    public function testValidLogin(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('art22@schmeler.com');

        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => $testUser->getUsername(),
            '_password' => 'password123',
        ]);
    
        $this->client->submit($form);
    
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
    $userRepository = static::getContainer()->get(UserRepository::class);
    $testUser = $userRepository->findOneByEmail('art22@schmeler.com');
    $this->client->loginUser($testUser);

        // Déconnexion
        $this->client->request('GET', '/logout');
        $this->assertResponseRedirects('/');
    }

    /**
     * Teste l'accès à une page protégée sans être connecté.
     */
    public function testAccessProtectedPageWithoutLogin(): void
    {
        $this->client->request('GET', 'admin/users/create');
        $this->assertResponseRedirects('/login');
    }

}
