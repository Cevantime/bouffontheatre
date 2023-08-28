<?php

namespace App\Admin;

use App\Entity\Artist;
use App\Entity\MediaGalleryItem;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\MediaBundle\Form\Type\MediaType;

class ProContactAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom'
            ])
            ->add('stageName', null, [
                'label' => 'Nom scène',
                'help' => 'Ce nom s\'affichera en priorité sur le site'
            ])
            ->add('email', null, [
                'label' => 'Adresse e-mail'
            ])
            ->add('jobs', CollectionType::class, [
                'label' => 'Métiers',
                'help' => 'Renseignez ici les métiers de ce contact pro',
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'admin_code' => 'admin.jobItem',
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('fullname');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('fullname', null, [
                'label' => "Nom"
            ])
            ->add('email', null, [
                'label' => 'Email'
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom de famille'
            ])
            ->add('stageName', null, [
                'label' => 'Nom de scène'
            ])
            ->add('email', null, [
                'label' => 'Adresse Email'
            ])
            ->add('jobs', null, [
                'label' => 'Métiers',
                'template' => 'sonata/array_show.html.twig'
            ])
        ;
    }
}
