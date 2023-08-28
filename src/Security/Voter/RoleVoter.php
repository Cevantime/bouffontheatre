<?php

namespace App\Security\Voter;

use App\Admin\RoleAdmin;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use App\Entity\Role;

class RoleVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_ROLE_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_ROLE_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_ROLE_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_ROLE_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_ROLE_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_ROLE_VIEW';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof RoleAdmin )
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Role );

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted('ROLE_ARTIST');
    }
}
