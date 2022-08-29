<?php

namespace App\Controller\admin;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\PrestationRepository;
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

        $reservation = new Reservation();

        //i used "php bin/console make:form to create a symfony class, she will conteint a form plan to create form
        //this name class is "ReservationType"

        $form = $this->createForm(ReservationType::class, $reservation);

        //Im giving a variable who conteint a form about Request class

        //My form is now avalable to handle and store the information
        $form->handleRequest($request);

        //If my form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            //im getting the form and i flush on database
            $reservation ->setStatut("en attente");
            $entityManager->persist($reservation);
            $entityManager->flush();
        }
        //register the reservation in database

        $this->addFlash('success', 'Votre rendez-vous as bien été enregirstrer');

        //I return the direction to my twig for the view
        return $this->render('admin/reservation.html.twig',[
        'form' => $form->CreateView()
        ]);
    }


    /**
     * @Route("/admin/list/reservation", name="admin_liste_reservation")
     */
    public function listReservation(ReservationRepository $reservationRepository,PrestationRepository $prestationRepository)
    {
        //Je demande au repository de m'afficher toutes les reservations
        $reservations = $reservationRepository ->findAll();
        $prestation = $prestationRepository -> findAll();
        //Je revoie vers mon twig
        return $this->render('admin/list_reservation.html.twig',[
        'reservations' => $reservations,
        'prestation' => $prestation
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