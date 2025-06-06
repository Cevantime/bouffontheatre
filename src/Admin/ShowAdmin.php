<?php

namespace App\Admin;

use App\Entity\ArtistItem;
use App\Entity\MediaGalleryItem;
use App\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Bundle\SecurityBundle\Security;

class ShowAdmin extends AbstractAdmin
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security, ?string $code = null, ?string $class = null, ?string $baseControllerName = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Informations', ['class' => 'col-md-8'])
            ->add('name', null, [
                'label' => 'Nom du projet'
            ])
            ->add('billetreducTitle', null, [
                'label' => 'Nom du projet sur billetreduc',
                'required' => false,
                'help' => 'Important pour retrouver les nombres de résas !'
            ])
            ->add('slug', null, [
                'label' => 'Slug',
                'required' => false,
                'help' => 'Laissez ce champs vide si vous ne savez pas ce que c\'est'
            ])
            ->add('punchline', null, [
                'label' => 'Phrase d\'accroche',
                'help' => 'Quelques mots d\'accroche. Ex: "À l\'odéon" ou "D\'après Dumas". Apparaît dans certaines galleries'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description'
            ])
            ->add('authors', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Auteur(s)'
            ], [
                'admin_code' => 'admin.artistItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('actors', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Acteur(s)'
            ], [
                'admin_code' => 'admin.artistItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('directors', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Metteur(s) en scène'
            ], [
                'admin_code' => 'admin.artistItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('roles', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Autres rôles',
                'help' => 'Permet de connaître les membre de l\'équipe et les rôles associés'
            ], [
                'admin_code' => 'admin.roleItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('shopLinks', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Lien(s) vers le(s) site(s) de billeterie'
            ], [
                'admin_code' => 'admin.linkItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('papers', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Revue de presse',
            ], [
                'admin_code' => 'admin.paperItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('featuredDocuments', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Documents supplémentaires',
                'help' => 'Documents supplémentaires que vous souhaitez voir apparaître (plan feu, dossier de presse etc.)'
            ], [
                'admin_code' => 'admin.mediaItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('featuredLinks', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Liens supplémentaires',
                'help' => 'Liens supplémentaires que vous souhaitez voir apparaître (facebook, instagram etc.)'
            ], [
                'admin_code' => 'admin.linkItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end()
            ->with('Visuels', ['class' => 'col-md-4'])
            ->add('banner', MediaType::class, [
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
                'required' => true,
                'label' => 'Bannière',
                'help' => 'Si définie, s\'affichera dans la bannière de la page spectacle'
            ])
            ->add('poster', MediaType::class, [
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
                'label' => 'Affiche',
                'required' => true
            ])
            ->add('gallery', ModelListType::class, [
                'required' => false,
                'label' => 'Galerie photo',
                'help' => 'S\'affichera dans la gallerie photo de la page spectacle',
                'btn_list' => false,
                'btn_delete' => false,
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
                'link_parameters' => ['provider' => 'sonata.media.provider.image'],
                'admin_code' => 'sonata.media.admin.gallery',
            ])
            ->end()
            ->with('Sessions')
            ->add('bookable', null, [
                'label' => 'Réservable en ligne',
                'required' => false,
                'help' => 'Indique si le site doit afficher les liens de réservations du spectacle'
            ])
            ->add('bookableOnline', null, [
                'label' => 'Réservable sur le site',
                'required' => false,
                'help' => 'Indique si le spectacle est réservable directement sur le site'
            ])
            ->add('sessions', CollectionType::class, [
                'label' => 'Sessions',
                'help' => 'Renseignez ici les périodes durant lesquelles le spectacle se joue (ou s\'est joué)',
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'admin_code' => 'admin.periodItem',
            ])
            ->end();
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $form
                ->with('Propriétaire')
                ->add('owner', ModelListType::class, [
                    'label' => 'Propriétaire'
                ])
                ->end();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name', null, [
            'show_filter' => true,
        ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('poster', MediaType::class, [
                'label' => 'Affiche',
                'template' => 'sonata/image_list.html.twig'
            ])
//            ->addIdentifier('id', null, [
//                'label' => "Id",
//                'route' => [
//                    'name' => 'edit',
//                ]
//            ])
            ->addIdentifier('name', null, [
                'label' => "Nom",
                'route' => [
                    'name' => 'edit',
                ]
            ])
            ->add('billetreducTitle', null, [
                'label' => 'Nom sur Billetreduc'
            ])
            ->add('authors', null, [
                'template' => 'sonata/artists_list.html.twig'
            ]);
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'id';
        $sortValues['_sort_order'] = 'DESC';
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->with('Informations', ['class' => 'col-md-8'])
            ->add('name', null, [
                'label' => 'Nom du projet'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'template' => 'sonata/html_text_show.html.twig'
            ])
            ->add('authors', null, [
                'label' => 'Auteur(s)',
                'template' => 'sonata/artists_show.html.twig'
            ])
            ->add('directors', null, [
                'label' => 'Metteur(s) en scène',
                'template' => 'sonata/artists_show.html.twig'
            ])
            ->add('actors', null, [
                'label' => 'Comédien(s)',
                'template' => 'sonata/artists_show.html.twig'
            ])
            ->add('roles', null, [
                'label' => 'Autres rôles',
                'template' => 'sonata/roles_show.html.twig'
            ])
            ->add('featuredDocuments', null, [
                'label' => 'Documents supplémentaires',
                'template' => 'sonata/documents_show.html.twig'
            ])
            ->end()
            ->with('Visuels', ['class' => 'col-md-4'])
            ->add('banner', MediaType::class, [
                'label' => 'Bannière',
                'template' => 'sonata/image_show.html.twig'
            ])
            ->add('poster', MediaType::class, [
                'label' => 'Affiche',
                'template' => 'sonata/image_show.html.twig'
            ])
            ->add('gallery', null, [
                'label' => 'Gallerie photo'
            ])
            ->end()
            ->with('Liens externes')
            ->add('shopLinks', null, [
                'label' => 'Sites de billeterie',
                'template' => 'sonata/links_show.html.twig'
            ])
            ->add('papers', null, [
                'label' => 'Revue(s) de presse',
                'template' => 'sonata/papers_show.html.twig'
            ])
            ->add('featuredLinks', null, [
                'label' => 'Liens supplémentaires',
                'template' => 'sonata/links_show.html.twig'
            ])
            ->end()
            ->with('Sessions')
            ->add('bookable', null, [
                'label' => 'Réservable'
            ])
            ->add('bookableOnline', null, [
                'label' => 'Réservable sur le site',
                'required' => false,
                'help' => 'Indique si le spectacle est réservable directement sur le site'
            ])
            ->add('sessions', CollectionType::class, [
                'label' => 'Sessions',
                'template' => 'sonata/sessions_show.html.twig'
            ])
            ->end();
    }


    protected function preValidate(object $object): void
    {
        /** @var Show $object */
        foreach ($object->getActors() as $actorItem) {
            /** @var ArtistItem $galleryItem */
            if ($actorItem->getArtist() === null) {
                $this->entityManager->remove($actorItem);
                $object->removeActor($actorItem);
            }
        }

        foreach ($object->getAuthors() as $authorItem) {
            /** @var ArtistItem $galleryItem */
            if ($authorItem->getArtist() === null) {
                $this->entityManager->remove($authorItem);
                $object->removeAuthor($authorItem);
            }
        }

        foreach ($object->getDirectors() as $directorItem) {
            /** @var ArtistItem $galleryItem */
            if ($directorItem->getArtist() === null) {
                $this->entityManager->remove($directorItem);
                $object->removeDirector($directorItem);
            }
        }

        foreach ($object->getShopLinks() as $shopLink) {
            /** @var ArtistItem $galleryItem */
            if ($shopLink->getLink() === null) {
                $this->entityManager->remove($shopLink);
                $object->removeShopLink($shopLink);
            }
        }

        foreach ($object->getPapers() as $paper) {
            if ($paper->getPaper() === null) {
                $this->entityManager->remove($paper);
                $object->removePaper($paper);
            }
        }

        foreach ($object->getRoles() as $role) {
            if ($role->getRole() === null) {
                $this->entityManager->remove($role);
                $object->removeRole($role);
            }
        }

        foreach ($object->getSessions() as $session) {
            if ($session->getPeriod() === null) {
                $this->entityManager->remove($session);
                $object->removeSession($session);
            }
        }

        foreach ($object->getFeaturedDocuments() as $featuredDocument) {
            if ($featuredDocument->getMedia() === null) {
                $this->entityManager->remove($featuredDocument);
                $object->removeFeaturedDocument($featuredDocument);
            }
        }


        foreach ($object->getFeaturedLinks() as $featuredLink) {
            if ($featuredLink->getLink() === null) {
                $this->entityManager->remove($featuredLink);
                $object->removeFeaturedLink($featuredLink);
            }
        }


        if ($object->getGallery()) {
            foreach ($object->getGallery()->getGalleryItems() as $galleryItem) {
                /** @var MediaGalleryItem $galleryItem */
                if ($galleryItem->getMedia() === null) {
                    $this->entityManager->remove($galleryItem);
                    $object->getGallery()->removeGalleryItem($galleryItem);
                }
            }
        }
    }
}
