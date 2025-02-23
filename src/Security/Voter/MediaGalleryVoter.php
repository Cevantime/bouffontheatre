<?php

namespace App\Security\Voter;

use App\Admin\MediaItemAdmin;
use App\Entity\Media;
use App\Entity\MediaGallery;
use App\Entity\MediaItem;
use App\Repository\ShowRepository;
use Sonata\MediaBundle\Admin\GalleryAdmin;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MediaGalleryVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_LIST';
    public const ADMIN_ALL = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_ALL';
    public const ADMIN_CREATE = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_CREATE';
    public const ADMIN_EDIT = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_EDIT';
    public const ADMIN_DELETE = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_DELETE';
    public const ADMIN_VIEW = 'ROLE_SONATA_MEDIA_ADMIN_GALLERY_VIEW';

    private Security $security;
    private ShowRepository $showRepository;

    public function __construct(Security $security, ShowRepository $showRepository)
    {
        $this->security = $security;
        $this->showRepository = $showRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof GalleryAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof MediaGallery);

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var MediaGallery $subject */
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::ADMIN_VIEW:
                return $this->security->isGranted('ROLE_ARTIST');
            case self::ADMIN_EDIT:
                $shows = $this->showRepository->findBy(['gallery' => $subject]);
                return count($shows) == 1 && $shows[0]->getOwner() === $token->getUser();
        }

        return false;
    }
}
