<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerNormalTest extends WebTestCase
{
    private $client;

    /**
     * Initialisation des ressources avant chaque test.
     */
    public function setUp(): void
    {
        $this->client = static::createClient();

        // Récupération du User
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('art22@schmeler.com');
        $this->client->loginUser($testUser);
    }

    // ------------------------------
    // Tests sur les utilisateurs
    // ------------------------------

    /**
     * Teste qu'un utilisateur normal ne peut pas accéder aux pages /admin/.
     */
    public function testUserCannotAccessAdminPages(): void
    {
        // Essayer d'accéder à une page admin
        $this->client->request('GET', '/admin/users');

        // Vérifie que l'utilisateur normal est redirigé vers une page d'erreur ou une autre page
        $this->assertResponseStatusCodeSame(403); // Accès interdit (403)
    }

    /**
     * Teste qu'un utilisateur normal ne peut pas accéder à la page /admin/user/create.
     */
    public function testUserCannotAccessAdminCreateUser(): void
    {
        $this->client->request('GET', '/admin/users/create');

        // Vérifie que l'utilisateur est redirigé ou obtient un code d'erreur
        $this->assertResponseStatusCodeSame(403); // Accès interdit (403)
    }

    /**
     * Teste qu'un utilisateur normal ne peut pas modifier un utilisateur.
     */
    public function testUserCannotEditUser(): void
    {
        // Essayer d'accéder à la page de modification de l'utilisateur
        $this->client->request('GET', '/admin/users/1/edit');

        // Vérifie que l'utilisateur normal obtient une erreur (403)
        $this->assertResponseStatusCodeSame(403); // Accès interdit (403)
    }

}
