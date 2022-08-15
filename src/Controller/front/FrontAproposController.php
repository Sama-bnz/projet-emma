<?php

namespace App\Controller\front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontAproposController extends AbstractController
{
    //Creating road "about me"" with URL and name
    /**
     * @Route("/apropos", name="apropos")
     */
//Creating the function
    public function aPropos()
    {
        //return to my twig
        return $this->render('front/apropos.html.twig');
    }
}