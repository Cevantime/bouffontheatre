<?php

namespace App\Security\Voter;

use App\Entity\Show;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PerformanceVoter extends Voter
{
    public const ADD_TO_PERFORMANCE = 'RESERVATION_ADD_TO_PERFORMANCE';

    private Security $security;

    function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ADD_TO_PERFORMANCE])
            && $subject instanceof \App\Entity\Performance;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ADD_TO_PERFORMANCE:
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                if($this->security->isGranted('ROLE_ARTIST')) {
                    return $subject->getRelatedProject() instanceof Show && $subject->getRelatedProject()->getOwner() === $token->getUser();
                }
                return $subject->getRelatedProject() instanceof Show && $subject->getRelatedProject()->isBookableOnline();
        }

        return false;
    }
}
