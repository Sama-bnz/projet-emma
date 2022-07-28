<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomeController extends AbstractController
{
    //Im creating my HomePage
    /**
     * @Route("/", name="home")
     */
    //Creating home function
    public function home()
    {
        //Return to my twig
        return $this->render('home.html.twig');
    }
}