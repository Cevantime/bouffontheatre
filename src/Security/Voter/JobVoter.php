<?php

namespace App\Security\Voter;

use App\Admin\JobAdmin;
use App\Admin\JobItemAdmin;
use App\Entity\Job;
use App\Entity\JobItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class JobVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_JOB_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_JOB_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_JOB_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_JOB_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_JOB_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_JOB_VIEW';

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
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof JobAdmin)
            || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Job);
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
            case self::ADMIN_EDIT:
                return $this->security->isGranted('ROLE_ARTIST');
        }

       return false;
    }
}
