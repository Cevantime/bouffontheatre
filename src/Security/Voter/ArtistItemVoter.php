<?php

namespace App\Security\Voter;

use App\Admin\ArtistAdmin;
use App\Admin\ArtistItemAdmin;
use App\Entity\Artist;
use App\Entity\ArtistItem;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ArtistItemVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_ARTISTITEM_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_ARTISTITEM_ALL';
    public const ADMIN_EDIT = 'ROLE_ADMIN_ARTISTITEM_EDIT';
    public const ADMIN_CREATE = 'ROLE_ADMIN_ARTISTITEM_CREATE';
    public const ADMIN_DELETE = 'ROLE_ADMIN_ARTISTITEM_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_ARTISTITEM_VIEW';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ArtistItemAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof ArtistItem);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }


        switch ($attribute) {
            case self::ADMIN_LIST:
            case self::ADMIN_VIEW:
                return $this->security->isGranted('ROLE_ARTIST');
            case self::ADMIN_CREATE:
                return false;
            case self::ADMIN_EDIT:
            case self::ADMIN_DELETE:
                /** @var User $user */
                $user = $token->getUser();
                /** @var ArtistItem $subject */

                $project = null;
                if($subject->getActedProject() !== null) {
                    $project = $subject->getActedProject();
                } elseif($subject->getAuthoredShow() !== null) {
                    $project = $subject->getAuthoredShow();
                } elseif ($subject->getDirectedProject() !== null) {
                    $project = $subject->getDirectedProject();
                }
                if($project !== null) {
                    return $project->getOwner() === $user;
                } else {
                    return true;
                }
        }

        return false;
    }
}
