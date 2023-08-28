<?php

namespace App\Admin;

use App\Entity\Artist;
use App\Entity\ContentItem;
use App\Entity\MediaGalleryItem;
use App\Entity\Page;
use App\Repository\GalleryItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Object\Metadata;
use Sonata\AdminBundle\Object\MetadataInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\MediaBundle\Form\Type\MediaType;
use Sonata\MediaBundle\Provider\Pool;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PageAdmin extends AbstractAdmin
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
        $form
            ->add('name', null, [
                'label' => 'Nom de la page',
                'disabled' =>  !$isSuperAdmin
            ])
            ->add('slug', null, [
                'label' => 'Clé unique',
                'disabled' => ! $isSuperAdmin
            ])
            ->add('contents',CollectionType::class, [
                'by_reference' => false,
                'label' => 'Contenus',
                'help' => 'Tous les contenus de cette page',
            ], [
                'admin_code' => 'admin.contentItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end();
    }

    protected function preValidate(object $object): void
    {
        /** @var Page $object */
        if ($object->getContents()) {
            foreach ($object->getContents() as $contentItem) {
                /** @var ContentItem $contentItem */
                if ($contentItem->getContent() === null) {
                    $this->entityManager->remove($contentItem);
                    $object->removeContent($contentItem);
                }
            }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('name', null, [
                'label' => 'Nom de la page'
            ])
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name', null, [
                'label' => "Nom de la page"
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name', null, [
                'label' => 'Nom de la page'
            ])
            ->add('slug', null, [
                'label' => 'Clé unique'
            ])
            ->add('contents', null, [
                'template' => 'sonata/content_show.html.twig'
            ])
        ;
    }
}
