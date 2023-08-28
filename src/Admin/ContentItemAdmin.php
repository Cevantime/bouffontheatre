<?php

namespace App\Admin;

use Doctrine\DBAL\Types\TextType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ContentItemAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('position',HiddenType::class)
            ->add('help', null, [
                'disabled' => true
            ])
            ->add('content', ModelListType::class, [
                'btn_list' => false,
                'btn_delete'=> false,
                'btn_add' => $this->isGranted('ROLE_SUPER_ADMIN') ? 'Ajouter' : false,
                'label' => 'Contenu'
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('content', ModelListType::class, [
                'btn_list' => false,
                'btn_delete'=> false,
                'label' => 'Contenu'
            ])
            ->add('help', TextType::class, [
                'label' => 'Indication'
            ])
        ;
    }
}
