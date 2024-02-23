<?php

namespace App\Form;

use App\DTO\ContractTheaterPart;
use App\Entity\Show;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractTheaterPartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('project', EntityType::class, [
                'class' => Show::class,
                'label' => 'Projet associé'
            ])
            ->add('performances', CollectionType::class, [
                'label' => 'Les représentations',
                'entry_type' => PerformanceType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('showServiceSession', TextType    ::class, [
                'label' => 'Date et heure de la session de service',
                'attr' => ['placeholder' => 'le 15 mai 2024 de 14h à 18h']
            ])

            ->add('contractCompanyPart', ContractCompanyPartType::class, [
                'label' => 'Information de la compagnie'
            ])
            ->add('contractConfig', ContractGlobalConfigType::class, [
                'label' => 'Configuration du contrat'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContractTheaterPart::class,
        ]);
    }
}
