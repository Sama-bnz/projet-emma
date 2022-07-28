<?php

namespace App\Controller\admin;

use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\PrestationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPrestationController extends AbstractController
{
    /**
     * @Route("/admin/list/prestations", name="admin_list_prestations")
     */
    public function listPrestation(PrestationRepository $prestationRepository)
    {
        $prestations =$prestationRepository -> findAll();
        return $this->render('admin/list_prestations.html.twig',[
            'prestations'=>$prestations
        ]);
    }








    /**
     * @Route("/admin/create/prestation", name="admin_create_prestation")
     */
    public function createPrestation(EntityManagerInterface $entityManager, Request $request)
    {
        //je créé une instance de la classe book (classe d'entité (celle qui as permis de crée la table))
//        dans le but de créer un nouvel article de la BDD (table book)

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
}



