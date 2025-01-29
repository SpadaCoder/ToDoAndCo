<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
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
    // Tests sur la création de tâche
    // ------------------------------

    public function testMakeTask()
    {
        // Accès à la page de création de tâche
        $crawler = $this->client->request('GET', '/tasks/create');

        // Récupération du formulaire et soumission
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Tâche test n°' . uniqid(),
            'task[content]' => 'Une nouvelle tâche test'
        ]);

        $this->client->submit($form);

        // Vérification de la redirection après la soumission
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
    }

    // ------------------------------
    // Tests sur la modification de tâche
    // ------------------------------

    public function testEditTask()
    {
        $crawler = $this->client->request('GET', '/tasks/1/edit');
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
        $crawler = $this->client->request('GET', '/tasks/1/toggle');
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }

    // ------------------------------
    // Tests sur la suppression de tâche
    // ------------------------------

    public function testDeniedDeleteTask()
    {
        $this->client->request('GET', '/tasks/2/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTask()
    {
        $taskRepository = static::getContainer()->get(\App\Repository\TaskRepository::class);

        $task = $taskRepository->findLastTask();

        $this->client->request('GET', '/tasks/' . $task->getId() . '/delete'); 
        $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testFailDeleteTask()
    {
        $this->client->request('GET', '/tasks/77/delete');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }
}
