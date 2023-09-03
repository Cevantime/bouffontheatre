<?php

namespace App\Admin;

use App\Entity\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Security;

class UserAdmin extends AbstractAdmin
{

    private Security $security;

    public function __construct(Security $security, ?string $code = null, ?string $class = null, ?string $baseControllerName = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->security = $security;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $roles = User::ROLES;

        if(!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            unset($roles['Super-administrateur']);
        }

        $form
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom'
            ])
            ->add('email', null, [
                'label' => 'Adresse Email'
            ])
            ->add('newsletter')
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => User::ROLES,
                'multiple' => true,
                'expanded' => true
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom'
            ])
            ->add('email', null, [
                'label' => 'Email'
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom'
            ])
            ->addIdentifier('email', null, [
                'label' => 'Email'
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom'
            ])
            ->add('email', null, [
                'label' => 'Adresse Email'
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => User::ROLES,
                'multiple' => true,
                'expanded' => true,
                'template' => 'sonata/admin_roles_show.html.twig'
            ]);
        ;
    }
}
