<?php

namespace App\Form;

use App\Entity\Contract;
use App\Entity\Project;
use App\Entity\Workflow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkflowContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('performances', CollectionType::class, [
                'label' => 'Les représentations',
                'entry_type' => PerformanceType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('showServiceSession', TextType::class, [
                'label' => 'Date et heure de la session de service',
                'attr' => ['placeholder' => 'le 15 mai 2024 de 14h à 18h']
            ])
            ->add('contractType', ChoiceType::class, [
                'choices' => [
                    'Contrat de co-réalisation avec minimum garanti' => Contract::TYPE_CO_PRODUCTION,
                    'Contrat de co-réalisation sans minimum garanti' => Contract::TYPE_CO_PRODUCTION_WITHOUT_MINIMUM,
                    'Contrat de location avec régisseur' => Contract::TYPE_RENT_WITH_STAGE_MANAGER,
                    'Contrat de location sans régisseur' => Contract::TYPE_RENT_WITHOUT_STAGE_MANAGER,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
        ]);
    }
}
