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
     * Cette fonction updateAdmin va permettre de modifier un administrateur grâce à un formulaire
     * @Route("/admin/update_admin/{id}", name="admin_update")
     */
    public function updateAdmin(Request $request, EntityManagerInterface $entityManager, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        //Je crée une variable et lui donne le formulaire "AdminType" qui est un formulaire de modification des données
        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);
        //Si le formulaire est envoyé et qu'il es valide
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);
            //je donne à mon mes variable mail et password les données correspondantes à mon formulaire
            $userPassword = $form->get('password')->getData();
            $userMail = $form->get('email')->getData();
            //je crée une variable hashedpawword et lui donne la fonction hashpassword pour crypter le mot de passe
            $hashedPassword = $userPasswordHasher->hashPassword($user, $userPassword);
            $user->setPassword($hashedPassword);
            $user->setEmail($userMail);
            //J'envoi les informations en base de donnée
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Modifié avec succès');
            //Je redirige la vue sur ma liste des administrateurs
            return $this->redirectToRoute('list_admins');

        }
        return $this->renderForm('admin/update_admin.html.twig', [
            'admin' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Cette fonction permet de supprimer un administrateur
     * @Route("/admin/delete_admin{id}", name="admin_delete")
     */
    public function deleteAdmin($id, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        //On récupère l'administrateur grace à son ID
        $user = $userRepository->find($id);
        if (!is_null($user)) {
            //Grâce à l'entity manager je supprime l'administrateur de la liste
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez bien supprimé votre admin');
            //Je redirige la vue vers la liste des administrateurs
            return $this->redirectToRoute('list_admins');
        } else {
            return new Response('Admin inexistant');
        }
    }
}
