<?php

namespace App\Admin;

use App\Entity\Contract;
use Doctrine\DBAL\Types\DateType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\Type\CollectionType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class WorkflowAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id', null, [
                'label' => "Id",
                'route' => [
                    'name' => 'edit',
                ]
            ])
            ->add('contract', ModelType::class, [
                'label' => 'Contrat associé',
            ])
            ->add('associatedShow', ModelType::class, [
                'label' => 'Projet associé',
            ])
            ->addIdentifier('createdAt', null, [
                'label' => 'Crée le',
                'route' => [
                    'name' => 'edit',
                ]
            ])
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        /** @var Contract $contract */
        $contract = $this->getSubject();
        $showMapper
            ->add('contract', ModelType::class, [
                'label' => 'Contrat associé'
            ])
            ->add('associatedShow', ModelType::class, [
                'label' => 'Projet associé',
            ])
            ->add('createdAt', null, [
                'label' => 'Crée le'
            ])
            ->end()
            ;

    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues['_sort_by'] = 'id';
        $sortValues['_sort_order'] = 'DESC';
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('closed', null, [
            'show_filter' => true,
            'label' => 'Clôturé'
        ]);
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        $filterValues['closed'] = [
            'value' => false,
        ];
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('edit');
        $collection->remove('create');

        $collection->add('edit', 'edit/{id}');
    }
}
