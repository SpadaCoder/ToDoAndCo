<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskController extends AbstractController
{

    /**
     * Gestionnaire d'entités pour l'accès à la base de données.
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Constructeur du contrôleur.
     *
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }


    /**
     * Affiche la liste de toutes les tâches.
     *
     * @return Response La page affichant la liste des tâches.
     */
    #[Route('/tasks', name: 'task_list')]
    public function list()
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();

        return $this->render('task/list.html.twig', compact('tasks'));
    }

    /**
     * Affiche la liste des tâches complétées.
     *
     * @return Response La page affichant les tâches complétées.
     */
    #[Route('/tasks/completed', name: 'task_completed_list')]
    public function listCompleted()
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['isDone' => 1]);

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Permet de créer une nouvelle tâche.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @return Response La page affichant le formulaire de création.
     */
    #[Route('/tasks/create', name: 'task_create')]
    public function create(Request $request)
    {
        // Vérifier si l'utilisateur est connecté.
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException('Vous devez être connecté pour créer une tâche.');
        }

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($this->getUser());
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Permet de modifier une tâche existante.
     *
     * @param Task $task La tâche à modifier.
     * @param Request $request La requête contenant les modifications.
     * @return Response La page du formulaire de modification.
     */
    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function edit(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render(
            'task/edit.html.twig',
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );
    }

    /**
     * Bascule l'état de complétion d'une tâche.
     *
     * @param Task $task La tâche à modifier.
     * @return Response Redirige vers la liste des tâches après modification.
     */
    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * Supprime une tâche si l'utilisateur a les droits nécessaires.
     *
     * @param Task $task La tâche à supprimer.
     * @return Response Redirige vers la liste des tâches après suppression.
     */
    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task)
    {
        // Vérifier si l'utilisateur est connecté.
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException('Vous devez être connecté pour supprimer une tâche.');
        }

        $user = $this->getUser();

        // Vérifier si la tâche est associée à l'utilisateur "anonyme".
        if ($task->getUser() && $task->getUser()->getUsername() === 'anonyme') {
            // Si l'utilisateur est un administrateur, on lui permet de supprimer.
            if (!$this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('error', 'Vous devez être administrateur pour supprimer cette tâche.');
                return $this->redirectToRoute('task_list');
            }
        } else {
            // Vérifier que l'utilisateur est le créateur de la tâche.
            if ($task->getUser() !== $user) {
                $this->addFlash('error', 'Vous ne pouvez supprimer que vos propres tâches.');
                return $this->redirectToRoute('task_list');
            }
        }

        // Si l'utilisateur a les droits, supprimer la tâche.
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
