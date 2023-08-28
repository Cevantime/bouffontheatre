<?php

namespace App\Security\Voter;

use App\Admin\ArtistAdmin;
use App\Entity\Artist;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ArtistVoter extends Voter
{
    public const VIEW_PROFILE = 'ARTIST_VIEW_PROFILE';
    public const ADMIN_LIST = 'ROLE_ADMIN_ARTIST_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_ARTIST_ALL';
    public const ADMIN_EDIT = 'ROLE_ADMIN_ARTIST_EDIT';
    public const ADMIN_CREATE = 'ROLE_ADMIN_ARTIST_CREATE';
    public const ADMIN_DELETE = 'ROLE_ADMIN_ARTIST_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_ARTIST_VIEW';

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
        return ($attribute == self::VIEW_PROFILE && $subject instanceof Artist)
            || (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ArtistAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Artist);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }


        /** @var User $user */
        $user = $token->getUser();

        switch ($attribute) {
            case self::VIEW_PROFILE:
                return $subject->hasFile();
            case self::ADMIN_LIST:
            case self::ADMIN_VIEW:
                return $this->security->isGranted('ROLE_ARTIST');
            case self::ADMIN_CREATE:
                return $user->getOwnedProjects()->count() > 0;
            case self::ADMIN_EDIT:
                /** @var Artist $subject */
                $user = $token->getUser();
                return $this->security->isGranted('ROLE_ARTIST') && $subject->getAssociatedUser() === $user || $user->getOwnedProjects()->count() > 0;
        }

        return false;
    }
}
