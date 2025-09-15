<?php

namespace App\Admin;

use App\Entity\Contract;
use Doctrine\DBAL\Types\DateType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContractAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, [
                'label' => "Id"
            ])
            ->addIdentifier('relatedProject', ModelType::class, [
                'label' => 'Project associé',
            ])
            ->add('contractDate', DateType::class, [
                'label' => 'Date de création',
                'template' => 'sonata/date_list.html.twig'
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        /** @var Contract $contract */
        $contract = $this->getSubject();
        $showMapper
            ->with('Le projet')
            ->add('relatedProject', ModelType::class, [
                'label' => 'Projet associé'
            ])
            ->add('performances', CollectionType::class, [
                'label' => 'Les représentations',
                'template' => 'sonata/dates_show.html.twig'
            ])
            ->add('showServiceSession', null, [
                'label' => 'Date et heure de la session de service'
            ])
            ->add('contractType', null, [
                'label' => 'Type de contrat',
                'template' => 'sonata/contract_type_show.html.twig'
            ])
            ->add('status', null, [
                'label' => 'Statut',
                'template' => 'sonata/contract_status_show.html.twig'
            ])

            ->add('fetchDataStatus', null, [
                'label' => 'Demande d\'informations',
                'template' => 'sonata/contract_status_show.html.twig'
            ])
            ->end()
            ->with('Informations données par le théâtre', ['class' => 'col-md-6'])
            ->add('theaterName', null, [
                'attr' => ['placeholder' => 'La compagnie du Bouffon Théâtre '],
                'label' => 'Nom de la compagnie du théâtre',
                'help' => 'Figure comme DIRECTEUR dans le contrat'
            ])
            ->add('theaterAddress', null, [
                'attr' => ['placeholder' => '26/28 rue de Meaux 75019 Paris '],
                'label' => 'Adresse du théâtre',
            ])
            ->add('showTheaterAvailability', null, [
                'label' => 'Heure de mise à disposition du théâtre',
            ])
            ->add('theaterSiret', null, [
                'attr' => ['placeholder' => '12 345 678 910 123'],
                'label' => 'SIRET de la compagnie du théâtre',
                'help' => 'Numéro de SIRET de la compagnie du théâtre'
            ])
            ->add('theaterPresident', null, [
                'attr' => ['placeholder' => 'Richard Arselin '],
                'label' => 'Nom du représentant légal de la compagnie du théâtre'
            ])
            ->add('theaterPhone', null, [
                'attr' => ['placeholder' => '06 23 45 67 89 '],
                'label' => 'Numéro de téléphone du théâtre'
            ])
            ->add('theaterEmail', null, [
                'attr' => ['placeholder' => 'contactbouffon@gmail.com'],
                'label' => 'Adresse email du théâtre'
            ])
            ->add('theaterBookingPhone', null, [
                'attr' => ['placeholder' => '06 98 76 54 32 '],
                'label' => 'Numéro de téléphone de réservation'
            ])
            ->add('showFullPrice', null, [
                'attr' => ['placeholder' => '17'],
                'label' => 'Prix des places plein tarif (€)'
            ])
            ->add('showHalfPrice', null, [
                'attr' => ['placeholder' => '12'],
                'label' => 'Prix des places en tarif réduit (€)'
            ])
            ->add('showDuration', null, [
                'attr' => ['placeholder' => '70'],
                'label' => 'Durée moyenne du spectacle (en min)'
            ])
            ->add('showMaxDuration', null, [
                'attr' => ['placeholder' => '80'],
                'label' => 'Durée maximale du spectacle (en min)'
            ])
            ->add('showInvitations', TextareaType::class, [
                'attr' => ['placeholder' => 'Les invitations sont strictement limitées aux professionnels : Journalistes et Programmateurs.'],
                'label' => 'À qui s\'adressent les invitations ?',
                'help' => 'Cette ligne apparaît dans le contrat pour préciser à qui s\'adressent les invitations'
            ])
            ->add('showTheaterShare', null, [
                'attr' => ['placeholder' => '100'],
                'label' => 'Part des recettes revenant au théâtre (en €)'
            ])
            ->add('showCompanyShare', null, [
                'attr' => ['placeholder' => '100'],
                'label' => 'Part des recettes revenant à la compagnie (en €)'
            ]);

        if ( ! $contract->isRent()) {
            $showMapper->add('showMinimumShare', null, [
                'attr' => ['placeholder' => '100'],
                'label' => 'Minimum garanti (en €)'
            ])
                ->add('showCompanySharePercent', null, [
                    'attr' => ['placeholder' => '50'],
                    'label' => 'Pourcentage du partage des recettes en faveur du théâtre (en %)'
                ])
                ->add('showTheaterSharePercent', null, [
                    'attr' => ['placeholder' => '50'],
                    'label' => 'Pourcentage du partage des recettes en faveur de la compagnie (en %)'
                ]);
        } else {
            if($contract->isRentWithStageManager()) {
                $showMapper->add('stageManagementInstallHourCount', null, [
                    'label' => 'Nombre d\'heures de régie d\'installation'
                ])
                ->add('stageManagementInstallPrice', null, [
                    'label' => 'Tarif horaire de la régie d\'installation'
                ])
                ->add('stageManagementShowHourCount', null, [
                    'label' => 'Nombre d\'heures de régie spectacle'
                ])
                ->add('stageManagementShowPrice', null, [
                    'label' => 'Tarif horaire de la régie spectacle'
                ]);
            }
            $showMapper->add('rentPrice', null, [
                'label' => 'Prix de la location pour un spectacle (en €)'
            ]);
        }

        $showMapper->add('contractCity', null, [
            'attr' => ['placeholder' => 'Paris'],
            'label' => 'Ville où est rédigée le contrat'
        ])
            ->add('tva', null, [
                'attr' => ['placeholder' => '20'],
                'label' => 'Pourcentage TVA (en %)'
            ])
            ->end()
            ->with('Informations données par la compagnie', ['class' => 'col-md-6'])
            ->add('companyName', null, [
                'label' => 'Nom de la compagnie',
                'attr' => ['placeholder' => 'Les belles âmes']
            ])
            ->add('companySiret', null, [
                'label' => 'Siret de la compagnie',
                'attr' => ['placeholder' => '12 345 678 910 111']
            ])
            ->add('companyApe', null, [
                'label' => 'Code APE',
                'attr' => ['placeholder' => '9001Z']
            ])
            ->add('companyLicense', null, [
                'label' => 'Licence',
                'attr' => ['placeholder' => 'PLATESV-D-2020-003722']
            ])
            ->add('companyPresident', null, [
                'label' => 'Représentant légal de la compagnie',
                'attr' => ['placeholder' => 'Charles Dupont']
            ])
            ->add('companyAddress', null, [
                'label' => 'Adresse de la compagnie',
                'attr' => ['placeholder' => '33 rue des Lices 75013 Paris']
            ])
            ->add('companyAssurance', null, [
                'label' => 'Assurance de la compagnie',
                'attr' => ['placeholder' => 'MAIF']
            ])
            ->add('companyPhone', null, [
                'label' => 'Numéro de téléphone',
                'attr' => ['placeholder' => '01 23 45 67 89']
            ])
            ->add('showName', null, [
                'label' => 'Nom du spectacle',
                'attr' => ['placeholder' => 'Ruy Blas']
            ])
            ->add('showAuthor', null, [
                'label' => 'Nom de l\'auteur',
                'attr' => ['placeholder' => 'Victor Hugo']
            ])
            ->add('showDirector', null, [
                'label' => 'Nom du ou des metteur(s) en scène',
                'attr' => ['placeholder' => 'Robert Hossein']
            ])
            ->add('showArtistCount', null, [
                'label' => 'Nombre d\'artistes'
            ])
            ->add('showRib', null, [
                'label' => 'IBAN de la compagnie'
            ]);

    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('edit');
        $collection->remove('create');
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('showName', null, [
            'show_filter' => true,
        ]);
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'id';
        $sortValues['_sort_order'] = 'DESC';
    }
}
