<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskControllerTest extends WebTestCase
{
    private $client;
    private $route;

    /**
     * Initialisation de l'instance de client avant chaque test.
     */
    public function setUp(): void
    {
        $this->route = '/tasks/1/edit';
        $this->client = static::createClient();
    }

    /**
     * Crée un utilisateur et l'authentifie.
     *
     * Cette méthode crée un utilisateur pour l'authentification dans les tests.
     */
    private function createUser(): User
    {
        $user = new User();
        $user->setEmail('Email' . uniqid() . '@test.com');
        $user->setUsername('Username' . uniqid());
        $user->setPassword('password');

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    /**
     * Connexion de l'utilisateur avant chaque test.
     */
    public function loginUser(): void
    {
        $user = $this->createUser(); 
        $this->client->loginUser($user);
    }

    /**
     * Connexion d'un administrateur avant chaque test.
     */
    public function loginAdmin(): void
    {
        $admin = new User();
        $admin->setEmail('AdminEmail' . uniqid() . '@test.com');
        $admin->setUsername('AdminUsername' . uniqid());
        $admin->setPassword('password');

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        $this->client->loginUser($admin);
    }

    // ------------------------------
    // Tests sur la création de tâche
    // ------------------------------

    public function testMakeTask()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Tâche test n°' . uniqid(),
            'task[content]' => 'Une nouvelle tâche test'
        ]);

        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
        $this->assertSelectorTextContains('div.alert-success', "Superbe ! Votre tâche a bien été envoyée");
    }

    // ------------------------------
    // Tests sur la modification de tâche
    // ------------------------------

    public function testEditTask()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', $this->route);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Test modification de la tâche',
            'task[content]' => 'Test modification de la tâche'
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    // ------------------------------
    // Tests sur l'activation de tâche
    // ------------------------------

    public function testToggleTask()
    {
        $this->loginUser();
        $crawler = $this->client->request('GET', '/tasks/1/toggle');
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    public function testDeniedDeleteTask()
    {
        $this->loginUser();
        $this->client->request('GET', '/tasks/2/delete');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    // ------------------------------
    // Tests sur la suppression de tâche
    // ------------------------------

    public function testDeleteTask()
    {
        $this->loginAdmin();
        $this->client->request('GET', '/tasks/2/delete'); 
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testFailDeleteTask()
    {
        $this->loginAdmin();
        $this->client->request('GET', '/tasks/77/delete');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testUserFailDeleteTask()
    {
        $this->loginUser();
        $this->client->request('GET', '/tasks/3/delete');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
