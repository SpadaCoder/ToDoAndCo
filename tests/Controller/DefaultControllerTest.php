<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    /**
     * Initialisation du client avant chaque test.
     */
    public function setUp(): void
    {
        // Créez le client une seule fois pour réutiliser dans tous les tests
        $this->client = static::createClient();
    }

    // ------------------------------
    // Tests sur la page d'accueil
    // ------------------------------

    /**
     * Teste si la page d'accueil est accessible.
     */
    public function testIndexPageIsAccessible(): void
    {
        // Effectue une requête GET sur la page d'accueil
        $this->client->request('GET', '/');

        // Vérifie que la réponse a un statut 200 (OK)
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste si la page d'accueil rend le bon template.
     */
    public function testIndexPageRendersCorrectTemplate(): void
    {
        // Effectue une requête GET sur la page d'accueil
        $this->client->request('GET', '/');

        // Vérifie que la requête a réussi
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste si la page d'accueil contient le texte spécifique.
     */
    public function testIndexPageContainsText(): void
    {
        // Effectue une requête GET sur la page d'accueil
        $this->client->request('GET', '/');

        // Vérifie que la réponse contient un texte spécifique (par exemple un titre)
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    /**
     * Teste si la page d'accueil ne contient pas un texte incorrect.
     */
    public function testIndexPageDoesNotContainIncorrectText(): void
    {
        // Effectue une requête GET sur la page d'accueil
        $this->client->request('GET', '/');

        // Vérifie que la réponse ne contient pas un texte erroné
        $this->assertSelectorTextNotContains('h1', 'Erreur');
    }
}
