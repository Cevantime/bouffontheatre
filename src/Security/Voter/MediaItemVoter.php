<?php

namespace App\Security\Voter;

use App\Admin\MediaItemAdmin;
use App\Entity\Media;
use App\Entity\MediaItem;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class MediaItemVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_MEDIAITEM_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_MEDIAITEM_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_MEDIAITEM_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_MEDIAITEM_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_MEDIAITEM_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_MEDIAITEM_VIEW';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof MediaItemAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof MediaItem);

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
                /** @var MediaItem $subject */
                return $subject->getFeaturingShow()->getOwner() === $token->getUser();
        }

        return false;
    }
}
