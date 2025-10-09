<?php

namespace App\Security\Voter;

use App\Admin\ArtistAdmin;
use App\Entity\Artist;
use App\Entity\ArtistItem;
use App\Entity\RoleItem;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
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
    public const CREATE = 'ARTIST_CREATE';

    /**
     * @param Security $security
     */
    public function __construct(
        private readonly Security $security,
        private readonly PropertyAccessorInterface $propertyAccessor,
    )
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return ($attribute == self::VIEW_PROFILE && $subject instanceof Artist)
            || (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ArtistAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Artist)
            || (in_array($attribute, [self::CREATE]))
            ;
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
            case self::CREATE:
                return $user->getOwnedProjects()->count() > 0;
            case self::ADMIN_EDIT:
                /** @var Artist $subject */
                $user = $token->getUser();
                if( ! $this->security->isGranted('ROLE_ARTIST')) {
                    return false;
                }
                if($subject->getAssociatedUser() === $user) {
                    return true;
                }
                $artists = [];
                $getArtistPredicate = fn(ArtistItem $artistItem) => $artistItem->getArtist();

                foreach ($user->getOwnedProjects() as $project) {
                    foreach (['authors', 'actors', 'directors'] as $artistField) {
                        /** @var ArrayCollection $artistItems */
                        $artistItems = $this->propertyAccessor->getValue($project, $artistField);
                        $artists = array_merge($artists, $artistItems->map($getArtistPredicate)->getValues());
                    }
                    $projectRoleItems = $project->getRoles();
                    $roleArtists = $projectRoleItems->map(fn(RoleItem $roleItem) => $roleItem->getRole()->getArtist())->getValues();
                    $artists = array_merge($artists, $roleArtists);
                }

                return in_array($subject, $artists);
        }

        return false;
    }
}
