<?php

namespace App\Form;

use App\DTO\ContractAdminPart;
use App\Entity\Contract;
use App\Entity\Show;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractAdminPartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('project', EntityType::class, [
                'class' => Show::class,
                'label' => 'Projet associé',
                'disabled' => true
            ])
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
//            ->add('minimumShare', CheckboxType::class, [
//                'label' => 'Inclure un minimum garanti'
//            ])

            ->add('contractCompanyPart', ContractCompanyPartAdminType::class, [
                'label' => 'Information de la compagnie'
            ])
            ->add('contractTheaterConfig', ContractGlobalConfigType::class, [
                'label' => 'Configuration du contrat'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContractAdminPart::class,
        ]);
    }
}
