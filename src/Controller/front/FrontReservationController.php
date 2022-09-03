<?php

namespace App\Controller\front;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontReservationController extends AbstractController

{
    /**
     * @Route("/reservation", name="reservation")
     * @throws Exception
     */
    public function reservation(EntityManagerInterface $entityManager,Request $request, ReservationRepository $reservationRepository)
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
            //je recupère les champs date et heure et je les assemble pour les sauvegarder en BDD
            $day = $form->get('date_reservation')->getData();
            $time = $form->get('heure_reservation')->getData();
            //Je sépare l'heure et les minutes dans deux variables différentes
            $timeSplit = preg_split('/:/', $time->format('H:i'));
            //Je vérifie qu'on a bien deux parties
            if(sizeof($timeSplit) == 2){
                $hour = $timeSplit[0];
                $minutes = $timeSplit[1];
                //Je change l'heure de la date entrée
                $day->setTime($hour, $minutes);
                if($day > new DateTime()){
                    //Puis j'affecte le résultat à reservation
                    $reservation->setDateReservation($day);
                    $entityManager->persist($reservation);
                    $entityManager->flush();
                    $this->addFlash('success', 'Votre rendez-vous a bien été enregistré');
                } else {
                    $this->addFlash('error', 'Un rendez-vous dans le passé ne peut pas être enregistré.');
                }
            }
        }
        //register the reservation in database
        //On enregistre la reservation dans la BDD
        return $this->render('front/reservation.html.twig', [
            'form' => $form->CreateView()
        ]);
    }

    /**
     * Je créer ma route et ma fonction déja reservé, je lui transmet les parametres request
     * @Route("/reservation/existing", name="reservation_existing")
     * @throws Exception
     */
    public function dejaReserve(Request $request, ReservationRepository $reservationRepository): \Symfony\Component\HttpFoundation\JsonResponse
    {
        //J'utilise la variable request pour récuperer le jour que je veux traiter
        $requestDay = $request->request->get('day');
        //ici je veux la date reçue en format date PHP
        $day = new DateTime();
        $day->setTimestamp($requestDay/1000);
        $dateFin = new DateTime();
        //Je creer la date de fin de la journée en mettant l'heure à 23h59
        $dateFin->setTimestamp($day->getTimestamp());
        $dateFin->setTime(23, 59, 59);
        //je vais chercher toutes les reservations de la journée (de minuit à minuit)
        $reservationsDone = $reservationRepository->getReservationDone($day,$dateFin);
        //date debut et fin
        $disabledDates = [];
        //Pour chaques reservation prises dans reservation
        foreach($reservationsDone as $reservation){
            //Je recupère la durée de la prestation
            $duration = $reservation->getPrestation()->getDuringTime()->format('H:i:s');
            //on formate la durée de la prestation
            $parts = explode(':',$duration);
            $dateInterval = new DateInterval('PT'.$parts[0].'H'.$parts[1].'M'.$parts[2].'S');
            //on met dans un tableau l'heure de début et l'heure de fin de la prestation (date de début + la durée de la prestation)
            $disabledDates[] = [
                $reservation->getDateReservation()->format('H:i'),
                $reservation->getDateReservation()->add($dateInterval)->format('H:i')
            ];
        }
        //On renvoit le tableau dans un json de tout les créneaux reservés
        return $this->json(['dates' => $disabledDates]);
    }
}