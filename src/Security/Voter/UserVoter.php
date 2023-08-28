<?php

namespace App\Security\Voter;

use App\Admin\ShowAdmin;
use App\Admin\UserAdmin;
use App\Entity\Show;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class UserVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_USER_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_USER_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_USER_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_USER_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_USER_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_USER_VIEW';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof UserAdmin )
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof User );

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::ADMIN_LIST:
                return $this->security->isGranted('ROLE_ARTIST');
            case self::ADMIN_CREATE:
            case self::ADMIN_VIEW:
                return $this->security->isGranted('ROLE_ADMIN');
            case self::ADMIN_EDIT:
            case self::ADMIN_DELETE:
                /** @var User $subject */
                return $subject === $token->getUser();
        }

        return false;
    }
}
