<?php

namespace App\Form;

use App\DTO\ContractGlobalConfig;
use App\Form\DataTransformer\AmountTransformer;
use App\Form\DataTransformer\PhoneTransformer;
use App\Form\DataTransformer\SiretTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractGlobalConfigType extends AbstractType
{

    public function __construct(
        private PhoneTransformer  $phoneTransformer,
        private AmountTransformer $amountTransformer,
        private SiretTransformer $siretTransformer
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('theaterName', TextType::class, [
                'attr' => ['placeholder' => 'La compagnie du Bouffon Théâtre '],
                'label' => 'Nom de la compagnie du théâtre',
                'help' => 'Figure comme DIRECTEUR dans le contrat'
            ])
            ->add('theaterAddress', TextareaType::class, [
                'attr' => ['placeholder' => '26/28 rue de Meaux 75019 Paris '],
                'label' => 'Adresse du théâtre',
            ])
            ->add('showTheaterAvailability', TextType    ::class, [
                'label' => 'Heure de mise à disposition du théâtre',
                'attr' => ['placeholder' => 'une heure avant la représentation']
            ])
            ->add('theaterSiret', TextType::class, [
                'attr' => ['placeholder' => '12 345 678 910 123'],
                'label' => 'SIRET de la compagnie du théâtre',
                'help' => 'Numéro de SIRET de la compagnie du théâtre'
            ])
            ->add('theaterPresident', TextType::class, [
                'attr' => ['placeholder' => 'Richard Arselin '],
                'label' => 'Nom du représentant légal de la compagnie du théâtre'
            ])
            ->add('theaterPhone', TelType::class, [
                'attr' => ['placeholder' => '06 23 45 67 89 '],
                'label' => 'Numéro de téléphone du théâtre'
            ])
            ->add('theaterEmail', EmailType::class, [
                'attr' => ['placeholder' => 'contactbouffon@gmail.com'],
                'label' => 'Adresse email du théâtre'
            ])
            ->add('theaterBookingPhone', TelType::class, [
                'attr' => ['placeholder' => '06 98 76 54 32 '],
                'label' => 'Numéro de téléphone de réservation'
            ])
            ->add('showFullPrice', TextType::class, [
                'attr' => ['placeholder' => '17'],
                'label' => 'Prix des places plein tarif (€)'
            ])
            ->add('showHalfPrice', TextType::class, [
                'attr' => ['placeholder' => '12'],
                'label' => 'Prix des places en tarif réduit (€)'
            ])
            ->add('showInvitations', TextareaType::class, [
                'attr' => ['placeholder' => 'Les invitations sont strictement limitées aux professionnels : Journalistes et Programmateurs.'],
                'label' => 'À qui s\'adressent les invitations ?',
                'help' => 'Cette ligne apparaît dans le contrat pour préciser à qui s\'adressent les invitations'
            ])
            ->add('showTheaterShare', TextType::class, [
                'attr' => ['placeholder' => '100'],
                'label' => 'Part des recettes revenant au théâtre (en €)'
            ])
            ->add('showCompanyShare', TextType::class, [
                'attr' => ['placeholder' => '100'],
                'label' => 'Part des recettes revenant à la compagnie (en €)'
            ])
            ->add('showMinimumShare', TextType::class, [
                'attr' => ['placeholder' => '100'],
                'label' => 'Minimum garanti (en €)'
            ])
            ->add('rentPrice', TextType::class, [
                'attr' => ['placeholder' => '100'],
                'label' => 'Tarif horaire de la location'
            ])
            ->add('showCompanySharePercent', TextType::class, [
                'attr' => ['placeholder' => '50'],
                'label' => 'Pourcentage du partage des recettes en faveur du théâtre (en %)'
            ])
            ->add('showTheaterSharePercent', TextType::class, [
                'attr' => ['placeholder' => '50'],
                'label' => 'Pourcentage du partage des recettes en faveur de la compagnie (en %)'
            ])
            ->add('contractCity', TextType::class, [
                'attr' => ['placeholder' => 'Paris'],
                'label' => 'Ville où est rédigée le contrat'
            ])
            ->add('tva', TextType::class, [
                'attr' => ['placeholder' => '20'],
                'label' => 'Pourcentage TVA (en %)'
            ])
            ->add('stageManagementInstallHourCount', null, [
                'label' => 'Nombre d\'heures de régie d\'installation'
            ])
            ->add('stageManagementShowHourCount', null, [
                'label' => 'Nombre d\'heures de régie spectacle'
            ])
            ->add('stageManagementInstallPrice', TextType::class, [
                'attr' => ['placeholder' => '20'],
                'label' => 'Tarif horaire du service de régie d\'installation dans le cas d\'un contrat avec régisseur'
            ])
            ->add('stageManagementShowPrice', TextType::class, [
                'attr' => ['placeholder' => '60'],
                'label' => 'Tarif horaire pour le service de régie spectacle dans le cas d\'un contrat avec régisseur'
            ])

            ;

        $builder->get('theaterSiret')->addViewTransformer($this->siretTransformer);

        foreach (['theaterPhone', 'theaterBookingPhone'] as $phoneField) {
            $builder->get($phoneField)->addViewTransformer($this->phoneTransformer);
        }

        foreach ([
                     'showFullPrice',
                     'showHalfPrice',
                     'showTheaterShare',
                     'showCompanyShare',
                     'showCompanySharePercent',
                     'showTheaterSharePercent',
                     'showMinimumShare',
                     'tva',
                    'stageManagementInstallPrice',
                    'stageManagementShowPrice'
                 ] as $amountField) {
            $builder->get($amountField)->addViewTransformer($this->amountTransformer);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContractGlobalConfig::class,
        ]);
    }
}
