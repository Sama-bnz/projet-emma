<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Prestation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('adresseMail')
            ->add('telephone')
            ->add('codePostal')
            //Je donne à ma date la possibilité d'etre choisie sur un grand calendrier, on peux donc choisir jour/mois/année avec un seul bouton
            ->add('date_reservation', DateType::class,['widget' => 'single_text', 'format' => 'dd/MM/yyyy', 'input_format' => 'd/M/y', 'html5' => false, 'mapped' => false])
            ->add('heure_reservation', TimeType::class, ['widget' => 'single_text', 'mapped' => false])
            ->add('prestation',EntityType::class,['class' =>Prestation::class,'choice_label'=>'name',])
            //required false permet de ne pas rendre obligatoire la saisie du champ
            ->add('message',TextareaType::class, ['required' => false])
            ->add('valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
