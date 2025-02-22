<?php

namespace App\Form;

use App\Entity\Performance;
use App\Entity\Reservation;
use App\Repository\PerformanceRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', null, [
                'label' => 'Nom'
            ])
            ->add('firstName', null, [
                'label' => 'Prénom'
            ])
            // ->add('tarif2', null, [
            //     "label" => "Nombre de places en tarifs plein (17€)"
            // ])
            ->add('tarif1', null, [
                // "label" => "Nombre de place au tarif réduit (12€)",
                "label" => "Nombre de places",
                // "help" => "Chômeur, RSA, intermittents, étudiants ou -26 ans"
            ])
            ->add('performance', EntityType::class, [
                'class' => Performance::class,
                'query_builder' => function (PerformanceRepository $rep): QueryBuilder {
                    return $rep->availablePerformancesQueryBuilder();
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider la réservation'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
