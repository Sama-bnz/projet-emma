<?php

namespace App\Controller\front;

use App\Entity\User;
use App\Form\AdminType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/create", name="create_user")
     */
    public function createUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        //je créé une nouvelle instance de la classe user
        $user = new User();

        // j'affecte le rôle user par défaut
        $user->setRoles([]);

        //J'appel mon gabari de formulaire à l'aide de createform
        $form=$this->createForm(RegistrationType::class, $user);

        //j'utile l'instance de classe request pour récupérer la valeur de mon formulaire
        $form->handleRequest($request);

        //si le formulaire est soumis et valide
        if($form->isSubmitted() && $form->isValid()){

            //je récupère le mot de passe depuis le formulaire
            $plainPassword= $form->get('password')->getData();

            //Je veux crypter mon mot de passe à l'aide de la fonction Hash
            $hashedPassword = $userPasswordHasher->hashPassword($user,$plainPassword);

            $user->setPassword($hashedPassword);

            //J'envoie les données du formulaire à la BDD
            $entityManager->persist($user);
            $entityManager->flush();

            //Petit message comme quoi la demande à bien été executée
            $this->addFlash('success', 'Votre compte as bien été créer!');

            //Je redirige vers la route list admin
            return $this->redirectToRoute("home");

        }

        //Je renvoi vers mon formulaire de creation d'admin
        return $this->render('front/create_user.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/update_user/{id}", name="update_user")
     */
    public function updateUser(Request $request, EntityManagerInterface $entityManager, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher)
    {
        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);
            $userPassword = $form->get('password')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user, $userPassword);
            $user->setPassword($hashedPassword);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Modifié avec succès');

            return $this->redirectToRoute('home');

        }
        return $this->renderForm('front/update_user.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/delete_user{id}", name="delete_user")
     */
    public function deleteUser($id, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $user = $userRepository->find($id);
        if (!is_null($user)) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez bien supprimé votre compte');
            return $this->redirectToRoute('contact');
        } else {
            return new Response('Compte inexistant');
        }
    }
}