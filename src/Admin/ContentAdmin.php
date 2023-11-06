<?php

namespace App\Admin;

use App\Entity\ArtistItem;
use App\Entity\Content;
use App\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ContentAdmin extends AbstractAdmin
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ?string $code = null, ?string $class = null, ?string $baseControllerName = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $isSuperAdmin = $this->isGranted('ROLE_SUPER_ADMIN');

        $form->add('type', ChoiceType::class, [
            'label' => 'Type',
            'choices' => Content::TYPES,
            'disabled' => !$isSuperAdmin
        ]);

        $form->add('name', null, [
            'label' => 'Nom',
            'disabled' => !$isSuperAdmin
        ]);

        if ($isSuperAdmin) {
            $form->add('slug', null, [
                'label' => 'Slug'
            ]);
        }

        $form
            ->add('help', null, [
                'label' => 'Indication',
                'disabled' => !$isSuperAdmin
            ])
            ->add('title', null, [
                'label' => 'Titre'
            ])
            ->add('text', CKEditorType::class, [
                'label' => 'Texte'
            ])
            ->add('image', MediaType::class, [
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
            ])
            ->add('projectGallery', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Projets'
            ], [
                'admin_code' => 'admin.projectItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->add('blogPostGallery', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Articles'
            ], [
                'admin_code' => 'admin.blogPostItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ]);
    }


    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', null, [
                'label' => 'Nom'
            ])
            ->add('help', null, [
                'label' => 'Indication'
            ]);
    }

    protected function configure(): void
    {
        $this->setTemplate('edit', 'sonata/edit_content.html.twig');
    }

    protected function preValidate(object $object): void
    {

        foreach ($object->getProjectGallery() as $projectItem) {
            if ($projectItem->getProject() === null) {
                $this->entityManager->remove($projectItem);
                $object->removeProjectGallery($projectItem);
            }
        }

        foreach ($object->getBlogPostGallery() as $blogPostItem) {
            if ($blogPostItem->getBlogPost() === null) {
                $this->entityManager->remove($blogPostItem);
                $object->removeBlogPostGallery($blogPostItem);
            }
        }
    }
}
