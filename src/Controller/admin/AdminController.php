<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\AdminType;
use App\Form\RegistrationType;
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
    public function listAdmin(UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        return $this -> render('admin/admins.html.twig',[
            'users'=>$users
        ]);
    }




    /**
     * @Route("/admin/create", name="admin_create_admin")
     */
    public function createAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        //je créé une nouvelle instance de la classe admin
        $admin = new User();

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
    public function updateAdmin(Request $request, EntityManagerInterface $entityManager, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);
            $userPassword = $form->get('password')->getData();
            $userMail = $form->get('email')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user, $userPassword);
            $user->setPassword($hashedPassword);
            $user->setEmail($userMail);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Modifié avec succès');

            return $this->redirectToRoute('list_admins');

        }
        return $this->renderForm('admin/update_admin.html.twig', [
            'admin' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/delete_admin{id}", name="admin_delete")
     */
    public function deleteAdmin($id, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->find($id);
        if (!is_null($user)) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez bien supprimé votre admin');
            return $this->redirectToRoute('list_admins');
        } else {
            return new Response('Admin inexistant');
        }
    }
}
