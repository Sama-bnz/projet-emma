<?php

namespace App\Controller\admin;

use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\CategoryPrestationRepository;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminPrestationController extends AbstractController
{
    /**
     * @Route("/admin/list/prestations", name="admin_list_prestations")
     */
    public function listPrestation(CategoryPrestationRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        return $this->render('admin/list_prestations.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/admin/prestation/{id}", name="admin_show_prestation")
     */
    public function showPrestation(PrestationRepository $prestationRepository,CategoryPrestationRepository $categoryRepository, $id)
    {
        $prestation = $prestationRepository->find($id);
        $categories = $categoryRepository->find($id);

        return $this->render('admin/show_prestation.html.twig', [
            'prestation' => $prestation,
            'category' => $categories,
            'category_prestation' => $categories
        ]);
    }





    /**
     * @Route("admin/delete/prestation{id}", name="admin_delete_prestation")
     */
    public function deletePrestation(PrestationRepository $prestationRepository, $id, EntityManagerInterface $entityManager)
    {
        {
            $prestation = $prestationRepository->find($id);

            if (!is_null($prestation)) {
                $entityManager->remove($prestation);
                $entityManager->flush();
                return $this->redirectToRoute('admin_dashboard');
            } else {
                $this->addFlash('success', "La prestation as déja été supprimé");
                return $this->redirectToRoute('admin_list_prestations');
            }
        }
    }



    /**
     * @Route("/admin/create/prestation", name="admin_create_prestation")
     */
    public function createPrestation(EntityManagerInterface $entityManager, Request $request)
    {
        //je créé une instance de la classe book (classe d'entité (celle qui as permis de crée la table))
//        dans le but de créer un nouvel article de la BDD

        $prestation = new Prestation();

// j'ai utilisé la ligne de cmd php bin/console make:form pour créer une classe symfony qui va contenir le "plan" de formulaire afin de créer les articles. C'est la classe ArticleType

        $form = $this->createForm(PrestationType::class, $prestation);

        //On donne à la variable qui contient le formulaire une instance de la classe Request pour que le formulaire puisse récuperer tout les données des inputs et faire les setters sur $article automatiquement.
        //Mon formulaire est maintenant capable de recuperer et stocker les infos
        $form->handleRequest($request);

        //Si le formulaire à été posté et que les données sont valide
        if ($form->isSubmitted() && $form->isValid()) {


            //On enregistre le book dans la BDD
            $entityManager->persist($prestation);
            $entityManager->flush();

            $this->addFlash('success', 'La prestation as bien été créer');
        }

        //j'affiche mon twig en lui passant une variable form qui contient la view du formulaire

        return $this->render("admin/create_prestation.html.twig", [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/update/prestation/{id}", name="admin_update_prestation")
     */
    public function updatePrestation($id, PrestationRepository  $prestationRepository, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger)
    {
        //Avec le repository je selectionne un book en fonction de l'ID
        $prestation = $prestationRepository->find($id);

//        j'ai utilisé la ligne de cmd php bin/console make:form pour créer une classe symfony qui va contenir le "plan" de formulaire afin de créer les articles. C'est la classe BookType

        $form = $this->createForm(PrestationType::class, $prestation);

        //On donne à la variable qui contient le formulaire une instance de la classe Request pour que le formulaire puisse récuperer tout les données des inputs et faire les setters sur $article automatiquement.
        //Mon formulaire est maintenant capable de recuperer et stocker les infos
        $form->handleRequest($request);

        //Si le formulaire à été posté et que les données sont valide
        if ($form->isSubmitted() && $form->isValid()) {
            //On enregistre le book dans la BDD
            $entityManager->persist($prestation);
            $entityManager->flush();

            $this->addFlash('success', 'La prestation as bien été modifiée!');
            return $this->redirectToRoute('admin_dashboard');
        }

        //j'affiche mon twig en lui passant une variable form qui contient la view du formulaire

        return $this->render("admin/update_prestation.html.twig", [
            'form' => $form->createView(),
            'prestation' => $prestation
        ]);
    }
}



