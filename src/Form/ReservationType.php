<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function __construct(private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', null, [
                'label' => 'Nom'
            ])
            ->add('firstName', null, [
                'label' => 'Prénom'
            ])->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'help' => 'Adresse mail à laquelle doit être envoyé l\'email de confirmation.'
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
