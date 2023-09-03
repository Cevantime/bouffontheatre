<?php

namespace App\Calendar\Service;

use App\Entity\Booking;
use Symfony\Bundle\SecurityBundle\Security;

class EventDisplayService
{
    public function __construct(
        private Security $security,
        private ColorService $colorService
    )
    {
    }

    public function getEventTitle(Booking $booking): string
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $booking->getTitle();
        }

        return $this->isBookingPublic($booking) ? $booking->getTitle() : 'Non précisé';

    }

    public function isBookingPublic(Booking $booking)
    {
        return in_array($booking->getType(), [
            Booking::TYPE_CONCERT,
            Booking::TYPE_SHOW,
            Booking::TYPE_REPETITION,
            Booking::TYPE_COURSE
        ]);
    }

    public function getEventColor(Booking $booking): string
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->colorService->getBookingColor($booking);
        }

        return $this->isBookingPublic($booking) ? $this->colorService->getBookingColor($booking) : 'gray';
    }
}
