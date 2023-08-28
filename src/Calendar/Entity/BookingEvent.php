<?php

namespace App\Calendar\Entity;

use CalendarBundle\Entity\Event;

class BookingEvent extends Event
{
    public const DATE_FORMAT = 'Y-m-d\\TH:i:s.uP';

    public function toArray(): array
    {
        $event = [
            'title' => $this->getTitle(),
            'start' => $this->getStart()->format(self::DATE_FORMAT),
            'allDay' => $this->isAllDay(),
        ];

        if (null !== $this->getEnd()) {
            $event['end'] = $this->getEnd()->format(self::DATE_FORMAT);
        }

        if (null !== $this->getResourceId()) {
            $event['resourceId'] = $this->getResourceId();
        }

        return array_merge($event, $this->getOptions());
    }
}
