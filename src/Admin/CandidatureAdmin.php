<?php

namespace App\Admin;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\MediaBundle\Form\Type\MediaType;

class CandidatureAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('offer', ModelListType::class, [
                'label' => 'Annonce associée'
            ])
            ->add('email', null, [
                'label' => 'Email'
            ])
            ->add('fullname', null, [
                'label' => 'Nom complet'
            ])
            ->add('comment', CKEditorType::class, [
                'label' => 'Commentaire'
            ])
            ->add('cv', MediaType::class, [
                'provider' => 'sonata.media.provider.file',
                'context' => 'default',
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('email', null, [
                'label' => 'Email'
            ])
            ->add('fullname', null, [
                'label' => 'Nom complet'
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('offer', ModelType::class, [
                'label' => 'Annonce associée'
            ])
            ->addIdentifier('email', null, [
                'label' => 'Email'
            ])
            ->add('fullname', null, [
                'label' => 'Nom complet'
            ])
            ->add('cv', MediaType::class, [
                'provider' => 'sonata.media.provider.file',
                'context' => 'default',
                'template' => 'sonata/pdf_list.html.twig'
            ]);
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('offer', ModelType::class, [
                'label' => 'Annonce associée'
            ])
            ->add('email', null, [
                'label' => 'Email'
            ])
            ->add('fullname', null, [
                'label' => 'Nom complet'
            ])
            ->add('comment', CKEditorType::class, [
                'template' => 'sonata/html_text_show.html.twig'
            ])
            ->add('cv', MediaType::class, [
                'provider' => 'sonata.media.provider.file',
                'context' => 'default',
                'template' => 'sonata/pdf_show.html.twig'
            ]);
    }
}
