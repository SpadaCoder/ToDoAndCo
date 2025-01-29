<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerAdminTest extends WebTestCase
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
        $testUser = $userRepository->findOneByEmail('admin@example.com');
        $this->client->loginUser($testUser);
    }

    // ------------------------------
    // Tests sur les utilisateurs
    // ------------------------------

    /**
     * Teste qu'un administrateur peut accéder aux pages /admin/.
     */
    public function testAdminCanAccessAdminPages(): void
    {
        // Accéder à la page de gestion des utilisateurs admin
        $this->client->request('GET', '/admin/users');

        // Vérifie que l'administrateur peut accéder à la page
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste qu'un administrateur peut accéder à la page /admin/user/create.
     */
    public function testAdminCanAccessAdminCreateUser(): void
    {
        $this->client->request('GET', '/admin/users/create');

        // Vérifie que l'administrateur peut accéder à la page
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste qu'un administrateur peut créer un utilisateur.
     */
    public function testAdminCanCreateUser(): void
    {
        // Accéder à la page de création d'utilisateur
        $crawler = $this->client->request('GET', '/admin/users/create');

        // Soumettre le formulaire de création d'utilisateur
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'newuser',
            'user[email]' => 'newuser@example.com',
            'user[password][first]' => 'newpassword',
            'user[password][second]' => 'newpassword',
        ]);

        // Soumettre le formulaire
        $this->client->submit($form);

        // Vérifie que l'utilisateur a été redirigé après la création
        $this->assertResponseRedirects('/admin/users');
    }

    /**
     * Teste qu'un administrateur peut modifier un utilisateur.
     */
    public function testAdminCanEditUser(): void
    {
        // Récupérer un utilisateur existant
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userToEdit = $entityManager->getRepository(User::class)->findOneByEmail('newuser@example.com');

        // Accéder à la page de modification de l'utilisateur
        $crawler = $this->client->request('GET', '/admin/users/' . $userToEdit->getId() . '/edit');

        // Soumettre le formulaire de modification
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'Update Name'. uniqid(),
            'user[email]' => 'updateduser'. uniqid() . '@example.com',
            'user[password][first]' => 'updatedpassword',
            'user[password][second]' => 'updatedpassword',
        ]);

        // Soumettre le formulaire
        $this->client->submit($form);

        // Vérifie que l'utilisateur a été redirigé après la modification
        $this->assertResponseRedirects('/admin/users');
    }

}
