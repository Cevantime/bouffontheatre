<?php

namespace App\Admin;

use App\Entity\MediaGalleryItem;
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

class ServiceAdmin extends AbstractAdmin
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ?string $code = null, ?string $class = null, ?string $baseControllerName = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Informations', ['class' => 'col-md-8'])
            ->add('name', null, [
                'label' => 'Nom du projet'
            ])
            ->add('slug', null, [
                'label' => 'Slug',
                'help' => 'Laissez ce champs vide si vous ne savez pas ce que c\'est'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description'
            ])
            ->add('roles', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Rôles',
                'help' => 'Permet de connaître les membre de l\'équipe et les rôles associés'
            ], [
                'admin_code' => 'admin.roleItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end()
            ->with('Visuels', ['class' => 'col-md-4'])
            ->add('banner', MediaType::class, [
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
                'label' => 'Bannière',
                'help' => 'Si définie, s\'affichera dans la bannière de la page spectacle'
            ])
            ->add('gallery', ModelListType::class, [
                'required' => false,
                'label' => 'Galerie photo',
                'help' => 'S\'affichera dans la gallerie photo de la page spectacle'
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
                'link_parameters' => ['provider' => 'sonata.media.provider.image'],
                'admin_code' => 'sonata.media.admin.gallery',
            ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('name');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', null, [
                'label' => "Nom"
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Informations', ['class' => 'col-md-8'])
            ->add('name', null, [
                'label' => 'Nom du projet'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'template' => 'sonata/html_text_show.html.twig'
            ])

            ->add('roles', null, [
                'label' => 'Rôles',
                'template' => 'sonata/roles_show.html.twig'
            ])
            ->end()
            ->with('Visuels', ['class' => 'col-md-4'])
            ->add('banner', MediaType::class, [
                'label' => 'Bannière',
                'template' => 'sonata/image_show.html.twig'
            ])
            ->add('gallery', null, [
                'label' => 'Gallerie photo'
            ])
            ->end();
    }


    protected function preValidate(object $object): void
    {
        foreach ($object->getRoles() as $role) {
            if ($role->getRole() === null) {
                $this->entityManager->remove($role);
                $object->removeRole($role);
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
