<?php

namespace App\Security\Voter;

use App\Admin\MediaItemAdmin;
use App\Entity\Media;
use App\Entity\MediaGalleryItem;
use App\Entity\MediaItem;
use App\Repository\ShowRepository;
use Proxies\__CG__\App\Entity\MediaGallery;
use Sonata\MediaBundle\Admin\GalleryAdmin;
use Sonata\MediaBundle\Admin\GalleryItemAdmin;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MediaGalleryItemVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ITEM_LIST';
    public const ADMIN_ALL = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ITEM_ALL';
    public const ADMIN_CREATE = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ITEM_CREATE';
    public const ADMIN_EDIT = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ITEM_EDIT';
    public const ADMIN_DELETE = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ITEM_DELETE';
    public const ADMIN_VIEW = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ITEM_VIEW';

    private Security $security;
    private ShowRepository $showRepository;

    public function __construct(Security $security, ShowRepository $showRepository)
    {
        $this->security = $security;
        $this->showRepository = $showRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof GalleryItemAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof MediaGalleryItem);

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var MediaGalleryItem $subject */
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::ADMIN_LIST:
            case self::ADMIN_CREATE:
            case self::ADMIN_VIEW:
                return $this->security->isGranted('ROLE_ARTIST');
            case self::ADMIN_EDIT:
            case self::ADMIN_DELETE:
                $relatedShows = $this->showRepository->findBy(['gallery' => $subject->getGallery()]);
                return count($relatedShows) === 1 && $relatedShows[0]->getOwner() == $token->getUser();
        }

        return false;
    }
}
