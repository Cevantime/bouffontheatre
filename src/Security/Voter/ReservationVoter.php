<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ReservationVoter extends Voter
{
    public const EDIT = 'RESERVATION_EDIT';
    public const ADD = 'RESERVATION_ADD';
    public const DELETE = 'RESERVATION_DELETE';
    public const LIST = 'RESERVATION_LIST';

    private Security $security;

    function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {

        return (in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Reservation)
            || (in_array($attribute, [self::ADD])
                && $subject == null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ADD:
                return $subject->isBookableOnline();

            default:
                return $this->security->isGranted('ROLE_ADMIN');
        }
    }
}
