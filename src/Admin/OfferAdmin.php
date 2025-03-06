<?php

namespace App\Admin;

use App\Entity\Artist;
use App\Entity\ContentItem;
use App\Entity\MediaGalleryItem;
use App\Entity\Page;
use App\Repository\GalleryItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Object\Metadata;
use Sonata\AdminBundle\Object\MetadataInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OfferAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('project', null, [
                'label' => 'Projet associé à cette annonce'
            ])
            ->add('title', null, [
                'label' => 'Titre de l\'annonce"'
            ])
            ->add('description',CKEditorType::class, [
                'label' => 'Description'
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('title', null, [
                'label' => 'Titre'
            ])
            ->add('project')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title', null, [
                'label' => "Titre"
            ])
            ->add('project')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('project')
            ->add('title', null, [
                'label' => 'Titre de l\'annonce'
            ])
            ->add('description', CKEditorType::class, [
                'template' => 'sonata/html_text_show.html.twig'
            ])
        ;
    }
}
