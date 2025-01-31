<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{

    /**
     * Gestionnaire d'entités Doctrine pour l'interaction avec la base de données.
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * Service de hachage des mots de passe.
     *
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;


    /**
     * Constructeur du contrôleur UserController.
     *
     * @param EntityManagerInterface $entityManager   Gestionnaire d'entités Doctrine.
     * @param UserPasswordHasherInterface $passwordHasher Service de hachage des mots de passe.
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        
    }


    /**
     * Affiche la liste des utilisateurs.
     *
     * @return Response
     */
    #[Route('admin/users', name: 'user_list')]
    public function list()
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', compact('users'));
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * @param Request $request Requête HTTP contenant les données du formulaire.
     * @return Response
     */
    #[Route('admin/users/create', name: 'user_create')]
    public function create(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Modifie un utilisateur existant.
     *
     * @param User $user L'utilisateur à modifier.
     * @param Request $request Requête HTTP contenant les données du formulaire.
     * @return Response
     */
    #[Route('admin/users/{id}/edit', name: 'user_edit')]
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
            }

            $this->entityManager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
