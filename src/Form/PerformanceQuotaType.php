<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Performance;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerformanceQuotaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quota', IntegerType::class, [
                'label' => 'Quota de la représentation',
                'help' => 'Le nombre de réservations disponibles sur le site pour cette représentation.',
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
