<?php

namespace App\Controller\admin;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminReservationController extends AbstractController
{
/**
 * @Route("/admin/reservation", name="admin_reservation")
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
            $reservation ->setStatut("en attente");
            $entityManager->persist($reservation);
            $entityManager->flush();
        }
        //register the reservation in database
            //On enregistre la reservation dans la BDD


        $this->addFlash('success', 'Votre rendez-vous as bien été enregirstrer');

        return $this->render('admin/reservation.html.twig',[
        'form' => $form->CreateView()
        ]);
    }


    /**
     * @Route("/admin/list/reservation", name="admin_liste_reservation")
     */
    public function listReservation(ReservationRepository $reservationRepository)
    {
        //Je demande au repository de m'afficher toutes les reservations
        $reservations = $reservationRepository ->findAll();
        //Je revoie vers mon twig
        return $this->render('admin/list_reservation.html.twig',[
        'reservations' => $reservations
        ]);
    }


    /**
     * @Route("/admin/cancel/reservation/{id}", name="admin_cancel_reservation")
     */
    //On supprime une reservation à l'aide de son id
    //Mélange de ArticleRepository pour le sélectionner puis EntityManager pour le supprimer.

    public function cancelReservation(ReservationRepository $reservationRepository, $id, EntityManagerInterface $entityManager)
    {
        $reservation = $reservationRepository->find($id);

        if (!is_null($reservation)) {
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('success', "Votre rendez-vous as bien été annulé");
            return $this->redirectToRoute('admin_liste_reservation');

        } else {
            $this->addFlash('success', "Le rendez-vous as déja été annulé");
            return $this->redirectToRoute('admin_liste_reservation');
        }
    }
}