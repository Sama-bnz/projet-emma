<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomeController extends AbstractController
{
    //Je créer une route home qui sera ma page d'acceuil
    /**
     * @Route("/", name="home")
     */
    //Je créer la fonction home
    public function home()
    {
        //Je renvoi vers mon twig
        return $this->render('home.html.twig');
    }
}