<?php

namespace App\Admin;

use App\Entity\Period;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PeriodAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('dateStart', null, [
                'label' => 'Date de début de la période'
            ])
            ->add('dateEnd', null, [
                'label' => 'Date de fin de la période'
            ])
            ->add('days', ChoiceType::class, [
                'multiple' => 'true',
                'expanded' => 'true',
                'choices' => Period::DAYS,
                'label' => 'jours'
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('dateStart', null, [
                'label' => 'Date de début'
            ])
            ->add('dateEnd', null, [
                'label' => 'Date de fin'
            ])
            ->add('days', null, [
                'label' => 'Jours',
                'template' => 'sonata/days_list.html.twig'
            ])
        ;
    }
}
