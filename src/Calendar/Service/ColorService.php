<?php

namespace App\Calendar\Service;

use App\Entity\Booking;

class ColorService
{
    const TYPE_TO_COLOR_MAP = [
        Booking::TYPE_APPOINTMENT => '#039be5',
        Booking::TYPE_COURSE => '#8e24aa',
        Booking::TYPE_REPETITION => '#33b679',
        Booking::TYPE_SHOW => '#d60000',
        Booking::TYPE_CONCERT => '#e67c73',
        Booking::TYPE_DEFAULT => '#039be5'
    ];

    const TYPE_TO_GOOGLE_ID_MAP = [
        Booking::TYPE_APPOINTMENT => 7,
        Booking::TYPE_COURSE => 3,
        Booking::TYPE_REPETITION => 2,
        Booking::TYPE_SHOW => 11,
        Booking::TYPE_CONCERT => 4
    ];

    public function getBookingColor(Booking $booking)
    {
        return self::TYPE_TO_COLOR_MAP[$booking->getType()] ?? 'gray';
    }

    public function getBookingGoogleColorId(Booking $booking)
    {
        return self::TYPE_TO_GOOGLE_ID_MAP[$booking->getType()] ?? null;
    }

    public function findTypeFromGoogleColorId($googleColorId)
    {
        $googleIdsToType = array_flip(self::TYPE_TO_GOOGLE_ID_MAP);
        if( ! array_key_exists($googleColorId, $googleIdsToType)) {
            return Booking::TYPE_DEFAULT;
        }

        return $googleIdsToType[$googleColorId];
    }
}
