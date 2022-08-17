<?php

namespace App\Controller\front;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontReservationController extends AbstractController

{
    /**
     * @Route("/reservation", name="reservation")
     */
    public function reservation(EntityManagerInterface $entityManager,Request $request)
    {
        //Im creating an entity class reservation, my object is to create a new reservation in database

        //je créé une instance de la classe reservation (classe d'entité (celle qui as permis de crée la table))
//        dans le but de créer une nouvelle reservation de la BDD (table reservation)

        $reservation = new Reservation();

        //i used "php bin/console make:form to create a symfony class, she will conteint a form plan to create form
        //this name class is "ReservationType"

        // j'ai utilisé la ligne de cmd php bin/console make:form pour créer une classe
        // symfony qui va contenir le "plan" de formulaire afin de créer les formulaires. C'est la classe ReservationType

        $form = $this->createForm(ReservationType::class, $reservation);

        //Im giving a variable who conteint a form about Request class

        //On donne à la variable qui contient le formulaire une instance de la classe Request pour que le formulaire puisse récuperer tout les données des inputs et faire les setters sur $article automatiquement.

        //My form is now avalable to handle and store the information

        //Mon formulaire est maintenant capable de recuperer et stocker les infos
        $form->handleRequest($request);

        //Si le formulaire à été posté et que les données sont valide
        if ($form->isSubmitted() && $form->isValid()) {
            //je recupere le formulaire et je lui ajoute le statut en attente en BDD
            $reservation->setStatut("en attente");
            $entityManager->persist($reservation);
            $entityManager->flush();
        }
        //register the reservation in database
        //On enregistre la reservation dans la BDD


        $this->addFlash('success', 'Votre rendez-vous as bien été enregirstrer');

        return $this->render('front/reservation.html.twig', [
            'form' => $form->CreateView()
        ]);
    }
}