<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;

class RoleAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('job', ModelType::class, [
                'label' => 'Métier'
            ])
            ->add('artist', ModelListType::class, [
                'label' => 'Artiste'
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('job', null, [
                'label' => 'Métier'
            ])
            ->add('artist', null, [
                'label' => 'Artiste'
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('job', ModelType::class, [
                'label' => 'Métier'
            ])
            ->add('artist', ModelListType::class, [
                'label' => 'Artiste'
            ])
        ;
    }
}
