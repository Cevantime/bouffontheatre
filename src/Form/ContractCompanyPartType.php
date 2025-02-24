<?php

namespace App\Form;

use App\DTO\ContractCompanyPart;
use App\Form\DataTransformer\PhoneTransformer;
use App\Form\DataTransformer\SiretTransformer;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

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
            ->add('showRib', TextType::class, [
                'label' => 'IBAN de la compagnie'
            ])
            ->add('showName', TextType::class, [
                'label' => 'Nom du spectacle',
                'attr' => ['placeholder' => 'Ruy Blas']
            ])
            ->add('showAuthors', ArtistMultipleAutocompleteField::class, [
                'label' => 'Nom du ou des auteur(s)',
                'attr' => [
                    'placeholder' => 'Victor Hugo',
                    'data-controller' => 'contract-artist-autocomplete',
                ]
            ])
            ->add('showDirectors', ArtistMultipleAutocompleteField::class, [
                'label' => 'Nom du ou des metteur(s) en scène',
                'attr' => [
                    'placeholder' => 'Robert Hossein',
                    'data-controller' => 'contract-artist-autocomplete',
                ]
            ])
            ->add('showArtists', ArtistMultipleAutocompleteField::class, [
                'label' => 'Artistes présents en scène'
            ])
            ->add('showPunchline', TextType::class, [
                'label' => 'Phrase d\'accroche',
                'help' => 'Une très courte phrase destinée à introduire votre spectacle'
            ])
            ->add('showDescription', CKEditorType::class, [
                'label' => 'Résumé',
                'help' => 'Le résumé qui apparaîtra dans la fiche du spectacle'
            ])
            ->add('showBanner', FileType::class, [
                'constraints' => [
                    new Assert\Image([
                        'minRatio' => 1.65,
                        'maxRatio' => 1.85,
                        'minRatioMessage' => 'Le ratio de l\'image est trop faible (elle n\'est pas assez large). Il doit être proche de 16/9.',
                        'maxRatioMessage' => 'Le ratio de l\'image est trop élevé (elle est trop large). Il doit être proche de 16/9.',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez fournir une image valide (formats acceptés : JPG, PNG).',
                        'maxSize' => '4M',
                        'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser 4 Mo.',
                    ]),
                ],
                'label' => 'Bannière affichée sur le site',
                'help' => 'Format 16/9. Ne doit pas excéder 4Mo'
            ])
            ->add('showPoster', FileType::class, [
                'constraints' => [
                    new Assert\Image([
                        'minRatio' => 0.65,
                        'maxRatio' => 0.8,
                        'minRatioMessage' => 'Le ratio de l\'image est trop faible (elle n\'est pas assez large). Il doit être proche de 16/9.',
                        'maxRatioMessage' => 'Le ratio de l\'image est trop élevé (elle est trop large). Il doit être proche de 16/9.',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez fournir une image valide (formats acceptés : JPG, PNG).',
                        'maxSize' => '4M',
                        'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser 4 Mo.',
                    ]),
                ],
                'label' => 'Votre affiche',
                'help' => 'Votre affiche doit être conforme à notre charte graphique et ne pas excéder 4Mo'
            ])
            ->add('showMedia', CollectionType::class, [
                'label' => 'Vos photos',
                'entry_type' => FileType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'constraints' => [
                        new Assert\Image([
                            // Le ratio 16/9 ≈ 1.78. Ici, on autorise un ratio entre 1.7 et 1.8.
                            'minRatio' => 1,
                            'maxRatio' => 1.85,
                            'minRatioMessage' => 'Le ratio de l\'image est trop faible (elle n\'est pas assez large). Il doit être proche de 16/9.',
                            'maxRatioMessage' => 'Le ratio de l\'image est trop élevé (elle est trop large). Il doit être proche de 16/9.',
                            'mimeTypes' => ['image/jpeg', 'image/png'],
                            'mimeTypesMessage' => 'Veuillez fournir une image valide (formats acceptés : JPG, PNG).',
                            'maxSize' => '4M',
                            'maxSizeMessage' => 'La taille de l\'image ne doit pas dépasser 4 Mo.',
                        ]),
                    ],
                ],
                'attr' => ['data-controller' => 'form-collection'],
                'help' => 'Vos photos doivent être dans un format paysage et ne pas excéder 4Mo'
            ]);

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
