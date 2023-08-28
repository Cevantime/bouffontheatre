<?php

namespace App\Security\Voter;

use App\Admin\ArtistAdmin;
use App\Admin\ContentAdmin;
use App\Admin\ContentItemAdmin;
use App\Entity\Artist;
use App\Entity\Content;
use App\Entity\ContentItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ContentVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_CONTENT_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_CONTENT_ALL';
    public const ADMIN_EDIT = 'ROLE_ADMIN_CONTENT_EDIT';
    public const ADMIN_CREATE = 'ROLE_ADMIN_CONTENT_CREATE';
    public const ADMIN_DELETE = 'ROLE_ADMIN_CONTENT_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_CONTENT_VIEW';

    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ContentAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Content);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::ADMIN_ALL:
            case self::ADMIN_CREATE:
            case self::ADMIN_EDIT:
                return $this->security->isGranted('ROLE_ADMIN');
        }

        return false;
    }
}
