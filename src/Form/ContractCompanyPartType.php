<?php

namespace App\Form;

use App\DTO\ContractCompanyPart;
use App\Form\DataTransformer\PhoneTransformer;
use App\Form\DataTransformer\SiretTransformer;
use App\Repository\ContractRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\UX\Cropperjs\Form\CropperType;
use App\Form\CroppableImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ContractCompanyPartType extends AbstractType
{
    public function __construct(
        private SiretTransformer $siretTransformer,
        private PhoneTransformer $phoneTransformer,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $companyPart = $builder->getData();

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
                'label' => 'Nom du ou des auteur(s)'
            ])
            ->add('showDirectors', ArtistMultipleAutocompleteField::class, [
                'label' => 'Nom du ou des metteur(s) en scène'
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

            ->add('showDuration', null, [
                'label' => 'Durée du spectacle',
                'help' => 'en minutes'
            ])
            ->add('showMaxDuration', null, [
                'label' => 'Durée maximale du spectacle',
                'help' => 'en minutes'
            ])
            ->add('showBanner', CroppableImageType::class, [
                'label' => 'Bannière affichée sur le site',
                'aspect_ratio' => 16 / 9,
                'existing_url' => $companyPart->showHasBanner ? $companyPart->showBannerUrl : null,
                'required' => ! $companyPart->showHasBanner,
                'help' => 'Uploadez et recadrez votre bannière au format 16/9. Maximum 4Mo',
            ])
            ->add('showPoster', CroppableImageType::class, [
                'label' => 'Votre affiche',
                'aspect_ratio' => 3 / 4,
                'existing_url' => $companyPart->showHasPoster ? $companyPart->showPosterUrl : null,
                'required' => ! $companyPart->showHasPoster,
                'help' => 'Uploadez et recadrez votre affiche au format portrait (3/4). Maximum 4Mo',
            ])
            ->add('showMedia', CollectionType::class, [
                'label' => 'Vos photos de galerie',
                'entry_type' => CroppableImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'aspect_ratio' => 16 / 9,
                ],
                'attr' => ['data-controller' => 'form-collection'],
                'help' => 'Uploadez vos photos et recadrez-les au format 16/9. Chaque photo sera automatiquement recadrée lors de la sélection.',
                'by_reference' => false,
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
