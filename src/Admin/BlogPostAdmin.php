<?php

namespace App\Admin;

use App\Entity\ArtistItem;
use App\Entity\MediaGalleryItem;
use App\Entity\Project;
use App\Entity\Show;
use App\Repository\ArtistItemRepository;
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
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\MediaBundle\Form\Type\MediaType;

class BlogPostAdmin extends AbstractAdmin
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ?string $code = null, ?string $class = null, ?string $baseControllerName = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Informations', ['class' => 'col-md-8'])
            ->add('title', null, [
                'label' => 'Titre du post'
            ])
            ->add('slug', null, [
                'label' => 'Slug',
                'required' => false,
                'help' => 'Laissez ce champs vide si vous ne savez pas ce que c\'est'
            ])
            ->add('excerpt', null, [
                'label' => 'Présentation de l\'article',
                'help' => 'Une courte description accrocheuse du contenu du post. Apparaît dans certaines galeries.'
            ])
            ->add('content', FormatterType::class, [
                'label' => 'Description',
                // 'config_name' => 'article_config',
                'format_field'   => 'contentFormatter',
                // 'format_field_options' => [
                //     'choices' => [
                //         'text' => 'Text',
                //         'markdown' => 'Markdown',
                //     ],
                //     'empty_data' => 'markdown',
                // ],
                'source_field' => 'rawContent',
                // 'source_field_options' => [
                //     'attr' => ['class' => 'span10', 'rows' => 20],
                // ],
                'listener' => true,
                'target_field' => 'content',
                'ckeditor_context' => 'article_config'
            ])
            ->add('image', MediaType::class, [
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
                'label' => 'Image'
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('title');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title', null, [
                'label' => "Nom"
            ])
            ->add('author');
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->with('Informations', ['class' => 'col-md-8'])
            ->add('title', null, [
                'label' => 'Titre du post'
            ])
            ->add('excerpt', CKEditorType::class, [
                'label' => 'Description'
            ])
            ->add('author', ModelListType::class, [
                'label' => 'Auteur',
            ])
            ->add('createdAt')
            ->add('updatedAt')
            ->end();
    }
}
