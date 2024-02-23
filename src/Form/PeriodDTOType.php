<?php

namespace App\Form;

use App\DTO\Period;
use Sonata\Form\Type\DateTimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodDTOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', DateTimeType::class, [
                'label' => 'Date de début',
                'input' => 'datetime_immutable',
            ])
            ->add('to', DateTimeType::class, [
                'label' => 'Date de fin',
                'input' => 'datetime_immutable',
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Period::class,
        ]);
    }
}
