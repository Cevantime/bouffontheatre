<?php

namespace App\Security\Voter;

use App\Admin\ShowAdmin;
use App\Entity\Show;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ShowVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_SHOW_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_SHOW_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_SHOW_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_SHOW_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_SHOW_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_SHOW_VIEW';
    public const INSIGHT_VIEW = 'ROLE_INSIGHT_SHOW_VIEW';

    public const BOOK_ONLINE = 'SHOW_BOOK_ONLINE';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ShowAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE, self::INSIGHT_VIEW, self::BOOK_ONLINE]) && $subject instanceof Show);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::ADMIN_CREATE:
                return false;
            case self::ADMIN_LIST:
            case self::ADMIN_VIEW:
                return $this->security->isGranted('ROLE_ARTIST');
            case self::ADMIN_EDIT:
            case self::ADMIN_DELETE:
                /** @var Show $subject */
                return $subject->getOwner() === $token->getUser();
            case self::BOOK_ONLINE:
                return $subject->isBookableOnline() || $this->security->isGranted('ROLE_ADMIN');
            case self::INSIGHT_VIEW:
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                return $subject->getOwner() === $this->security->getUser();
        }

        return false;
    }
}
