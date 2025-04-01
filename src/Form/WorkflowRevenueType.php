<?php

namespace App\Form;

use App\DTO\WorkflowRevenue;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkflowRevenueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('revenueTickBossFile', null, [
                'label' => 'Document de recette Tickboss',
                'help' => 'Ce document est celui généré par Tickboss et sera envoyé à la compagnie et à Richard'
            ])
            ->add('copyrightApplicable', CheckboxType::class, [
                'label' => 'Droits d\'auteur applicables',
                'required' => false
            ])
            ->add('retirementContribApplicable', CheckboxType::class, [
                'label' => 'Contribution retraite applicable',
                'required' => false
            ])
            ->add('agessaContribApplicable', CheckboxType::class, [
                'label' => 'Contribution diffuseur Agessa applicable',
                'required' => false
            ])
            ->add('performances', CollectionType::class, [
                'entry_type' => PerformanceRevenueType::class,
                'label' => 'Déclaration par représentation'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Déclarer les recettes'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkflowRevenue::class,
        ]);
    }
}
