<?php

namespace App\Security\Voter;

use App\Admin\MediaItemAdmin;
use App\Entity\Media;
use App\Entity\MediaItem;
use Proxies\__CG__\App\Entity\MediaGallery;
use Sonata\MediaBundle\Admin\GalleryAdmin;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class MediaGalleryVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_LIST';
    public const ADMIN_ALL = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ALL';
    public const ADMIN_CREATE = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_CREATE';
    public const ADMIN_EDIT = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_EDIT';
    public const ADMIN_DELETE = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_DELETE';
    public const ADMIN_VIEW = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_VIEW';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof GalleryAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof MediaGallery);

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
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
                /** @var MediaGallery $subject */
                return $subject->getArtist()->getAssociatedUser() === $token->getUser();
        }

        return false;
    }
}
