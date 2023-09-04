<?php

namespace App\Calendar\Subscriber;

use App\Calendar\Entity\BookingEvent;
use App\Calendar\Service\ColorService;
use App\Calendar\Service\EventDisplayService;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private BookingRepository $bookingRepository,
        private EventDisplayService $eventDisplayService
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();

        // Modify the query to fit to your entity and needs
        // Change booking.beginAt by your start date property
        $bookings = $this->bookingRepository->getEventsBetween($start, $end);

        foreach ($bookings as $booking) {
            // this create the events with your data (here booking data) to fill calendar
            $bookingEvent = new BookingEvent(
                $this->eventDisplayService->getEventTitle($booking),
                $booking->getBeginAt(),
                $booking->getEndAt() // If the end date is null or not defined, a all day event is created.
            );
            $color = $this->eventDisplayService->getEventColor($booking);

            $bookingEvent->setOptions([
                'backgroundColor' => $color,
                'borderColor' => $color,
            ]);

            $bookingEvent->addOption('id', $booking->getId());

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($bookingEvent);
        }
    }
}
