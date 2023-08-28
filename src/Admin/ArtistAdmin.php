<?php

namespace App\Admin;

use App\Entity\Artist;
use App\Entity\MediaGalleryItem;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ArtistAdmin extends AbstractAdmin
{
    private EntityManagerInterface $entityManager;
    private Pool $pool;

    public function __construct(EntityManagerInterface $entityManager, Pool $pool, ?string $code = null, ?string $class = null, ?string $baseControllerName = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->entityManager = $entityManager;
        $this->pool = $pool;
    }

    public function getObjectMetadata(object $object): MetadataInterface
    {
        /** @var Artist $object */
        $media = $object->getPhoto();

        if (!$media) {
            return parent::getObjectMetadata($object);
        }

        $provider = $this->pool->getProvider($media->getProviderName());

        $url = $provider->generatePublicUrl($media, $provider->getFormatName($media, 'admin'));

        return new Metadata($media->getName(), $media->getDescription(), $url);
    }


    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Infos', ['class' => 'col-md-6'])
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom'
            ])
            ->add('stageName', null, [
                'label' => 'Nom de scène',
                'help' => 'Ce nom s\'affichera en priorité sur le site'
            ])
            ->add('jobs', null, [
                'label' => 'Métier(s)',
                'help' => 'S\'affichera dans la fiche perso, si activée'
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Masculin' => Artist::GENDER_MALE,
                    'Féminin' => Artist::GENDER_FEMALE,
                    'Non précisé' => null
                ]
            ])
            ->add('biography', CKEditorType::class, [
                'label' => 'Biographie'
            ])
            ->add('hasFile', null, [
                'label' => 'A une fiche perso',
                'help' => 'La fiche de l\'artiste apparaîtra sur le site'
            ])
            ->add('links', CollectionType::class, [
                'by_reference' => false,
                'label' => 'Liens',
                'help' => 'Liens que vous souhaitez voir apparaître (facebook, instagram etc.)'
            ], [
                'admin_code' => 'admin.linkItem',
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
            ])
            ->end()
            ->with('Photos', ['class' => 'col-md-6'])
            ->add('photo', MediaType::class, [
                'provider' => 'sonata.media.provider.image',
                'context' => 'default',
            ])
            ->add('gallery', ModelListType::class, [
                'required' => false,
                'label' => 'Galerie photo',
                'help' => 'S\'affichera dans la fiche perso, si celle-ci est active',
                'btn_list' => false
            ], [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
                'link_parameters' => ['provider' => 'sonata.media.provider.image'],
                'admin_code' => 'sonata.media.admin.gallery',
            ])
            ->end()
            ->with('Utilisateur associé', ['class' => 'col-md-6'])
            ->add('associatedUser', ModelListType::class, [
                'required' => false,
                'label' => 'Utilisateur associé',
                'help' => 'Vous pouvez créer à la volée un profil utilisateur pour cet artiste si celui souhaite pouvoir accéder au back office',
            ], [
                'admin_code' => 'admin.user',
            ])
            ->end();
    }

    protected function preValidate(object $object): void
    {
        /** @var Artist $object */
        if ($object->getLinks()) {
            foreach ($object->getLinks() as $linkItem) {
                if ($linkItem->getLink() === null) {
                    $this->entityManager->remove($linkItem);
                    $object->getLinks()->removeElement($linkItem);
                }
            }
        }
        if ($object->getGallery()) {
            /** @var Artist $object */
            foreach ($object->getGallery()->getGalleryItems() as $galleryItem) {
                /** @var MediaGalleryItem $galleryItem */
                if ($galleryItem->getMedia() === null) {
                    $this->entityManager->remove($galleryItem);
                    $object->getGallery()->removeGalleryItem($galleryItem);
                }
            }
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom'
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('photo', ModelType::class, [
                'label' => 'Photo',
                'template' => 'sonata/image_list.html.twig'
            ])
            ->addIdentifier('fullname', null, [
                'label' => "Nom"
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('photo', ModelType::class, [
                'photo' => 'Photo',
                'template' => 'sonata/image_show.html.twig'
            ])
            ->add('firstname', null, [
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'label' => 'Nom de famille'
            ])
            ->add('stageName', null, [
                'label' => 'Nom de scène'
            ])
            ->add('jobs', null, [
                'label' => 'Métiers',
                'template' => 'sonata/jobs_show.html.twig'
            ])
            ->add('gender', null, [
                'label' => 'Genre'
            ])
            ->add('gallery', null, [
                'label' => 'Gallerie de photo'
            ])
            ->add('biography', CKEditorType::class, [
                'template' => 'sonata/html_text_show.html.twig'
            ])
            ->add('links', null, [
                'template' => 'sonata/links_show.html.twig'
            ]);
    }
}
