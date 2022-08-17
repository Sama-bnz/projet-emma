<?php

namespace App\Controller\admin;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Form\RegistrationType;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/admins", name="list_admins")
     */
    public function listAdmin(AdminRepository $adminRepository)
    {
        $admins = $adminRepository->findAll();

        return $this -> render('admin/admins.html.twig',[
            'admins'=>$admins
        ]);
    }




    /**
     * @Route("/admin/create", name="admin_create_admin")
     */
    public function createAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        //je créé une nouvelle instance de la classe admin
        $admin = new Admin();

        // j'affecte le rôle d'admin par défaut
        $admin->setRoles(["ROLE_ADMIN"]);

        //J'appel mon gabari de formulaire à l'aide de createform
        $form=$this->createForm(RegistrationType::class, $admin);

        //j'utile l'instance de classe request pour récupérer la valeur de mon formulaire
        $form->handleRequest($request);

        //si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()){

            //je récupère le mot de passe depuis le formulaire
            $plainPassword= $form->get('password')->getData();

            //Je veux crypter mon mot de passe à l'aide de la fonction Hash
            $hashedPassword = $userPasswordHasher->hashPassword($admin,$plainPassword);

            $admin->setPassword($hashedPassword);

            //J'envoie les données du formulaire à la BDD
            $entityManager->persist($admin);
            $entityManager->flush();

            //Petit message comme quoi la demande à bien été executée
            $this->addFlash('success', 'Votre admin as été créer!');

            //Je redirige vers la route list admin
            return $this->redirectToRoute("list_admins");

        }

        //Je renvoi vers mon formulaire de creation d'admin
        return $this->render('admin/insert_admin.html.twig',[
            'form'=>$form->createView()
        ]);
    }


    /**
     * @Route("/admin/update_admin/{id}", name="admin_update")
     */
    public function updateAdmin(Request $request, EntityManagerInterface $entityManager, Admin $admin, AdminRepository $adminRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $adminRepository->add($admin, true);
            $adminPassword = $form->get('password')->getData();
            $adminMail = $form->get('email')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($admin, $adminPassword);
            $admin->setPassword($hashedPassword);
            $admin->setEmail($adminMail);
            $entityManager->persist($admin);
            $entityManager->flush();

            $this->addFlash('success', 'Modifié avec succès');

            return $this->redirectToRoute('list_admins');

        }
        return $this->renderForm('admin/update_admin.html.twig', [
            'admin' => $admin,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/delete_admin{id}", name="admin_delete")
     */
    public function deleteAdmin($id, AdminRepository $adminRepository, EntityManagerInterface $entityManager)
    {
        $admin = $adminRepository->find($id);
        if (!is_null($admin)) {
            $entityManager->remove($admin);
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez bien supprimé votre admin');
            return $this->redirectToRoute('list_admins');
        } else {
            return new Response('Admin inexistant');
        }
    }
}
