<?php

namespace App\Security\Voter;

use App\Admin\ShowAdmin;
use App\Entity\Show;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ShowVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_SHOW_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_SHOW_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_SHOW_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_SHOW_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_SHOW_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_SHOW_VIEW';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ShowAdmin )
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Show );

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
                /** @var Show $subject */
                return $subject->getOwner() === $token->getUser();
        }

        return false;
    }
}
