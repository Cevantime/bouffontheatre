<?php

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;

class PaperAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('extract', null, [
                'label' => 'Extrait'
            ])
            ->add('link', ModelListType::class, [
                'label' => 'Lien'
            ])
        ;
    }


    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('extract', null, [
                'label' => 'Extrait'
            ])
            ->add('link', ModelListType::class, [
                'label' => 'Lien'
            ])
        ;
    }
}
