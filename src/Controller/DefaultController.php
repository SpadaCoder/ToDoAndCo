<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{


    /**
     * Affiche la page d'accueil.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route('/', name: 'homepage')]
    public function index()
    {
        return $this->render('default/index.html.twig');

    }

    
}
