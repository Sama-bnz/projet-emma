<?php

namespace App\Controller\admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/admins", name="admin_list_admins")
     */
    public function listAdmin(UserRepository $userRepository)
    {
        $admins = $userRepository->findAll();

        return $this -> render('admin/admins.html.twig',[
            'admins'=>$admins
        ]);
    }
}