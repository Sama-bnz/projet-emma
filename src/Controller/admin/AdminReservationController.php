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
        //Je crée une entité de la classe réservation, l'objectif est de créer une nouvelle réservation dans la BDD
        $reservation = new Reservation();

        //J'utilise la ligne de code "php bin/console make:form afin de créer une classe symfony, elle contiendra un formulaire de réservation, son nom sera "ReservationType"
        $form = $this->createForm(ReservationType::class, $reservation);

        //Je crée une variable qui contient mon formulaire de la classe Request
        //Mon formulaire est maintenant capable de capter et de stocker les informations
        $form->handleRequest($request);

        //Si mon formulaire est posté et que les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            //On enregistre le formulaire dans la BDD
            $reservation ->setStatut("en attente");
            $entityManager->persist($reservation);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Votre rendez-vous as bien été enregirstrer');

        //On retourne vers mon twig qui représente ma vue
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