<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReservationVoter extends Voter
{
    public const EDIT = 'RESERVATION_EDIT';
    public const DELETE = 'RESERVATION_DELETE';

    private Security $security;

    function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {

        return (in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Reservation);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'RESERVATION_EDIT':
            case 'RESERVATION_DELETE':
                return $subject->getAuthor() === $token->getUser();
        }
    }
}
