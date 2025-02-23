<?php

namespace App\Form;

use App\DTO\ContractCompanyPart;
use App\Entity\Artist;
use App\Form\DataTransformer\PhoneTransformer;
use App\Form\DataTransformer\SiretTransformer;
use Phalcon\Text;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractCompanyPartType extends AbstractType
{
    public function __construct(
        private SiretTransformer $siretTransformer,
        private PhoneTransformer $phoneTransformer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Nom de la compagnie',
                'attr' => ['placeholder' => 'Les belles âmes']
            ])
            ->add('companySiret', TextType::class, [
                'label' => 'Siret de la compagnie',
                'attr' => ['placeholder' => '12 345 678 910 111']
            ])
            ->add('companyApe', TextType::class, [
                'label' => 'Code APE',
                'attr' => ['placeholder' => '9001Z']
            ])
            ->add('companyLicense', TextType::class, [
                'label' => 'Licence',
                'attr' => ['placeholder' => 'PLATESV-D-2020-003722']
            ])
            ->add('companyPresident', TextType::class, [
                'label' => 'Représentant légal de la compagnie',
                'attr' => ['placeholder' => 'Charles Dupont']
            ])
            ->add('companyAddress', TextType::class, [
                'label' => 'Adresse de la compagnie',
                'attr' => ['placeholder' => '33 rue des Lices 75013 Paris']
            ])
            ->add('companyAssurance', TextType::class, [
                'label' => 'Assurance de la compagnie',
                'attr' => ['placeholder' => 'MAIF']
            ])
            ->add('companyPhone', TextType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => '01 23 45 67 89']
            ])
            ->add('showName', TextType::class, [
                'label' => 'Nom du spectacle',
                'attr' => ['placeholder' => 'Ruy Blas']
            ])
            ->add('showAuthors', ArtistMultipleAutocompleteField::class, [
                'label' => 'Nom de l\'auteur',
                'attr' => ['placeholder' => 'Victor Hugo']
            ])
            ->add('showDirectors', ArtistMultipleAutocompleteField::class, [
                'label' => 'Nom du ou des metteur(s) en scène',
                'attr' => ['placeholder' => 'Robert Hossein']
            ])
            ->add('showArtists', ArtistMultipleAutocompleteField::class, [
                'label' => 'Artistes présents en scène'
            ])
            ->add('showRib', TextType::class, [
                'label' => 'IBAN de la compagnie'
            ])
        ;

        $builder->get('companySiret')->addViewTransformer($this->siretTransformer);

        $builder->get('companyPhone')->addViewTransformer($this->phoneTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContractCompanyPart::class,
        ]);
    }
}
