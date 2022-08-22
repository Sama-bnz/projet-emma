<?php

namespace App\Controller\admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */

    public function dashboard(UserRepository $userRepository)
    {
        return $this-> render('admin/home.html.twig',[
            'users' => $userRepository -> findAll()
        ]);

    }
}