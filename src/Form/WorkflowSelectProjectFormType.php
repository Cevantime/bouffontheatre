<?php

namespace App\Form;

use App\DTO\WorkflowSelectProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkflowSelectProjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('show', WorkflowShowAutoCompleteField::class, [
                'label' => 'Sélectionnez ou ajoutez le spectacle associé à ce projet',
            ])
            ->add('owner', WorkflowUserAutocompleteField::class, [
                'label' => 'Sélectionnez ou ajouttez le propriétaire de ce projet',
                'placeholder' => 'Georges Clooney',
                'help' => 'Si le projet a déjà un propriétaire, celui-ci sera remplacé par le propriétaire sélectionné'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkflowSelectProject::class,
        ]);
    }
}
