<?php

namespace App\Security\Voter;

use App\Admin\OfferAdmin;
use App\Admin\PaperAdmin;
use App\Admin\ShowAdmin;
use App\Entity\Offer;
use App\Entity\Paper;
use App\Entity\PaperItem;
use App\Entity\Show;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class PaperVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_PAPER_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_PAPER_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_PAPER_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_PAPER_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_PAPER_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_PAPER_VIEW';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof PaperAdmin )
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Paper );

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {

        return $this->security->isGranted('ROLE_ARTIST');
    }
}
