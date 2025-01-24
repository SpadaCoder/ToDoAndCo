<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $adminTestUser;
    private $normalUser;

    /**
     * Initialisation des ressources avant chaque test.
     */
    public function setUp(): void
    {
        $this->client = static::createClient(); // Création du client de test

        // Création d'un utilisateur administrateur avec le rôle ROLE_ADMIN
        $this->adminTestUser = new User();
        $this->adminTestUser->setEmail('TestAdmin@example.com');
        $this->adminTestUser->setUsername('TestAdmin');
        $this->adminTestUser->setPassword(password_hash('adminpassword', PASSWORD_BCRYPT));
        $this->adminTestUser->setRoles(['ROLE_ADMIN']); // Attribution du rôle admin

        // Création d'un utilisateur normal sans rôle admin
        $this->normalUser = new User();
        $this->normalUser->setEmail('user@example.com');
        $this->normalUser->setUsername('user');
        $this->normalUser->setPassword(password_hash('userpassword', PASSWORD_BCRYPT));

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($this->adminTestUser);
        $entityManager->persist($this->normalUser);
        $entityManager->flush();
    }

    /**
     * Nettoyage des ressources après chaque test.
     */
    public function tearDown(): void
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->remove($this->adminTestUser);
        $entityManager->remove($this->normalUser);
        $entityManager->flush();
    }

    // ------------------------------
    // Tests sur les utilisateurs
    // ------------------------------

    /**
     * Teste qu'un utilisateur normal ne peut pas accéder aux pages /admin/.
     */
    public function testUserCannotAccessAdminPages(): void
    {
        $this->loginUser(); 

        // Essayer d'accéder à une page admin
        $crawler = $this->client->request('GET', '/admin/users');

        // Vérifie que l'utilisateur normal est redirigé vers une page d'erreur ou une autre page
        $this->assertResponseStatusCodeSame(403); // Accès interdit (403)
    }

    /**
     * Teste qu'un utilisateur normal ne peut pas accéder à la page /admin/user/create.
     */
    public function testUserCannotAccessAdminCreateUser(): void
    {
        $this->loginUser(); 

        $crawler = $this->client->request('GET', '/admin/user/create');

        // Vérifie que l'utilisateur est redirigé ou obtient un code d'erreur
        $this->assertResponseStatusCodeSame(403); // Accès interdit (403)
    }

    /**
     * Teste qu'un administrateur peut accéder aux pages /admin/.
     */
    public function testAdminCanAccessAdminPages(): void
    {
        $this->loginAdmin(); 

        // Accéder à la page de gestion des utilisateurs admin
        $crawler = $this->client->request('GET', '/admin/users');

        // Vérifie que l'administrateur peut accéder à la page
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste qu'un administrateur peut accéder à la page /admin/user/create.
     */
    public function testAdminCanAccessAdminCreateUser(): void
    {
        $this->loginAdmin(); 

        $crawler = $this->client->request('GET', '/admin/user/create');

        // Vérifie que l'administrateur peut accéder à la page
        $this->assertResponseIsSuccessful();
    }

    /**
     * Teste qu'un administrateur peut créer un utilisateur.
     */
    public function testAdminCanCreateUser(): void
    {
        $this->loginAdmin(); // Connexion de l'administrateur

        // Accéder à la page de création d'utilisateur
        $crawler = $this->client->request('GET', '/admin/user/create');

        // Soumettre le formulaire de création d'utilisateur
        $form = $crawler->selectButton('Créer')->form([
            'user[username]' => 'newuser',
            'user[email]' => 'newuser@example.com',
            'user[password][first]' => 'newpassword',
            'user[password][second]' => 'newpassword',
        ]);

        // Soumettre le formulaire
        $this->client->submit($form);

        // Vérifie que l'utilisateur a été redirigé après la création
        $this->assertResponseRedirects('/admin/users');

        // Vérifie que l'utilisateur a bien été ajouté à la base de données
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $newUser = $entityManager->getRepository(User::class)->findOneBy(['username' => 'newuser']);
        $this->assertNotNull($newUser);
        $this->assertEquals('newuser@example.com', $newUser->getEmail());
    }

    /**
     * Teste qu'un administrateur peut modifier un utilisateur.
     */
    public function testAdminCanEditUser(): void
    {
        $this->loginAdmin();

        // Récupérer un utilisateur existant
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userToEdit = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);

        // Accéder à la page de modification de l'utilisateur
        $crawler = $this->client->request('GET', '/admin/user/' . $userToEdit->getId() . '/edit');

        // Soumettre le formulaire de modification
        $form = $crawler->selectButton('Mettre à jour')->form([
            'user[username]' => 'updateduser',
            'user[email]' => 'updateduser@example.com',
            'user[password][first]' => 'updatedpassword',
            'user[password][second]' => 'updatedpassword',
        ]);

        // Soumettre le formulaire
        $this->client->submit($form);

        // Vérifie que l'utilisateur a été redirigé après la modification
        $this->assertResponseRedirects('/admin/users');

        // Vérifie que l'utilisateur a bien été mis à jour dans la base de données
        $updatedUser = $entityManager->getRepository(User::class)->findOneBy(['username' => 'updateduser']);
        $this->assertNotNull($updatedUser);
        $this->assertEquals('updateduser@example.com', $updatedUser->getEmail());
    }

    /**
     * Teste qu'un utilisateur normal ne peut pas créer un utilisateur.
     */
    public function testUserCannotCreateUser(): void
    {
        $this->loginUser(); 

        // Essayer d'accéder à la page de création d'un utilisateur
        $crawler = $this->client->request('GET', '/admin/user/create');

        // Vérifie que l'utilisateur est redirigé ou obtient un code d'erreur
        $this->assertResponseStatusCodeSame(403); // Accès interdit (403)
    }

    /**
     * Teste qu'un utilisateur normal ne peut pas modifier un utilisateur.
     */
    public function testUserCannotEditUser(): void
    {
        $this->loginUser(); 

        // Récupérer un utilisateur existant
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userToEdit = $entityManager->getRepository(User::class)->findOneBy(['username' => 'user']);

        // Essayer d'accéder à la page de modification de l'utilisateur
        $crawler = $this->client->request('GET', '/admin/user/' . $userToEdit->getId() . '/edit');

        // Vérifie que l'utilisateur normal obtient une erreur (403)
        $this->assertResponseStatusCodeSame(403); // Accès interdit (403)
    }

    // ------------------------------
    // Méthodes pour simuler la connexion
    // ------------------------------

    /**
     * Connecte un utilisateur normal.
     */
    private function loginUser(): void
    {
        $this->client->loginUser($this->normalUser);
    }

    /**
     * Connecte un administrateur.
     */
    private function loginAdmin(): void
    {
        $this->client->loginUser($this->adminTestUser);
    }
}
