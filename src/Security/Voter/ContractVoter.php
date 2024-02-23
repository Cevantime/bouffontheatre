<?php

namespace App\Security\Voter;

use App\Admin\ContractAdmin;
use App\Entity\Contract;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ContractVoter extends Voter
{
    public const ADMIN_LIST = 'ROLE_ADMIN_CONTRACT_LIST';
    public const ADMIN_ALL = 'ROLE_ADMIN_CONTRACT_ALL';
    public const ADMIN_CREATE = 'ROLE_ADMIN_CONTRACT_CREATE';
    public const ADMIN_EDIT = 'ROLE_ADMIN_CONTRACT_EDIT';
    public const ADMIN_DELETE = 'ROLE_ADMIN_CONTRACT_DELETE';
    public const ADMIN_VIEW = 'ROLE_ADMIN_CONTRACT_VIEW';

    public const CONTRACT_COMPANY_COMPLETE = 'CONTRACT_COMPANY_COMPLETE';

    public function __construct(
        private Security $security
    )
    {
    }


    protected function supports(string $attribute, mixed $subject): bool
    {
        return (in_array($attribute, [self::ADMIN_ALL, self::ADMIN_LIST, self::ADMIN_CREATE]) && $subject instanceof ContractAdmin)
        || (in_array($attribute, [self::ADMIN_VIEW, self::ADMIN_EDIT, self::ADMIN_DELETE]) && $subject instanceof Contract);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return $this->security->isGranted('ROLE_ADMIN');

    }
}
