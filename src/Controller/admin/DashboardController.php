<?php

namespace App\Controller\admin;

use App\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     */

    public function dashboard(AdminRepository $adminRepository)
    {
        return $this-> render('admin/home.html.twig',[
            'admin' => $adminRepository -> findAll()
        ]);

    }
}