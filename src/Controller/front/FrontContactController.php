<?php

namespace App\Controller\front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */

    //methode appelée par la route
    public function contact(){
        return $this ->render('front/contact.html.twig');
    }
}