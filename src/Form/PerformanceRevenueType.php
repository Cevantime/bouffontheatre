<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Performance;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerformanceRevenueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('performedAt', DateTimeType::class, [
                'label' => 'Date de représentation',
                'disabled' => true
            ])
            ->add('grossRevenue', NumberType::class, [
                'label' => 'Recette brute'
            ])
            ->add('fullPriceCount', null, [
                'label' => 'Nombre de tarifs pleins',
            ])
            ->add('halfPriceCount', null, [
                'label' => 'Nombre de tarif réduits',
                'help' => 'Ceci regroupe toute forme de remise'
            ])
            ->add('appPriceCount', null, [
                'label' => 'Nombre de tarif Internet',
                'help' => 'TivketNunc par ex'
            ])
            ->add('taxFreePriceCount', null, [
                'label' => 'Nombre de détax',
                'help' => 'Ceci regroupe détax (résident/Éva)'
            ])
            ->add('freeCount', null, [
                'label' => 'Nombre d\'invitations/gratuits'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Performance::class,
        ]);
    }
}
