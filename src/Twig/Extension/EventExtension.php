<?php

namespace App\Twig\Extension;

use App\Calendar\Service\EventDisplayService;
use App\Entity\Booking;
use App\Twig\Runtime\EventExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EventExtension extends AbstractExtension
{

    public function __construct(
        private EventDisplayService $eventDisplayService
    )
    {
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('event_title', [$this, 'getEventTitle']),
        ];
    }

    public function getEventTitle(Booking $booking)
    {
        return $this->eventDisplayService->getEventTitle($booking);
    }
}
