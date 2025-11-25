<?php

namespace App\Form;

use App\Entity\Performance;
use App\Entity\Reservation;
use App\Repository\PerformanceRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('placeCount', null, [
                "label" => "Nombre de places",
            ])
            ->add('price', ChoiceType::class, [
                'label' => 'Tarif',
                'choices' => array_flip(Reservation::AVAILABLE_PRICES),
                'help' => 'Le tarif réduit concerne les demandeurs d\'emploi, les étudiants et les intermittents du spectacle',
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false,
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
