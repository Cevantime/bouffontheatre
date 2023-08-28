<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class LinkAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', null, [
                'label' => 'Titre'
            ])
            ->add('url', null, [
                'label' => 'Url'
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('title',null, [
                'label' => 'Titre'
            ])
            ->add('url',null, [
                'label' => 'Url'
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('title',null, [
                'label' => 'Titre'
            ])
            ->add('url',null, [
                'label' => 'Url'
            ])
        ;
    }
}
