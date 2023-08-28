<?php

namespace App\Security\Voter;

use App\Admin\OfferAdmin;
use App\Admin\PaperAdmin;
use App\Admin\ProjectAdmin;
use App\Admin\ShowAdmin;
use App\Entity\Offer;
use App\Entity\Paper;
use App\Entity\PaperItem;
use App\Entity\Project;
use App\Entity\Show;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ProjectVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_PROJECT_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_PROJECT_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_PROJECT_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_PROJECT_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_PROJECT_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_PROJECT_VIEW';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ProjectAdmin )
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Project );

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case self::ADMIN_ALL:
            case self::ADMIN_LIST:
            case self::ADMIN_VIEW:
            case self::ADMIN_CREATE:
            case self::ADMIN_EDIT:
            case self::ADMIN_DELETE:
                return $this->security->isGranted('ROLE_ADMIN');
        }

        return false;
    }
}
