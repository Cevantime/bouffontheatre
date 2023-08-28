<?php

namespace App\Security\Voter;

use App\Admin\ArtistAdmin;
use App\Admin\CandidatureAdmin;
use App\Entity\Candidature;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CandidatureVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_CANDIDATURE_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_CANDIDATURE_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_CANDIDATURE_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_CANDIDATURE_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_CANDIDATURE_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_CANDIDATURE_VIEW';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof CandidatureAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Candidature);

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
            case self::ADMIN_EDIT:
            case self::ADMIN_DELETE:
                $user = $token->getUser();
                /** @var Candidature $subject */
                return $this->security->isGranted('ROLE_ARTIST') && $subject->getOffer()->getProject()->getOwner() === $user;
        }

        return false;
    }
}
