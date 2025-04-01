<?php

namespace App\Form;

use App\DTO\TickbossRevenueExcel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TickbossRevenueExcelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('revenueExcel', FileType::class, [
                'label' => 'État simplifié des recettes excel'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Préremplir le formulaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TickbossRevenueExcel::class,
        ]);
    }
}
