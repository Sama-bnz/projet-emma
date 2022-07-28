<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminAproposController extends AbstractController
{
    //Creating road "about me"" with URL and name
/**
 * @Route("/admin/apropos", name="admin_apropos")
 */
//Creating the function
    public function aPropos()
    {
        //return to my twig
        return $this->render('admin/apropos.html.twig');
    }
}