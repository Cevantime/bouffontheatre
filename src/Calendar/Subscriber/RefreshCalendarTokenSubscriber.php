<?php

namespace App\Calendar\Subscriber;

use App\Calendar\Service\CalendarService;
use App\Calendar\Service\GoogleAuthenticationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RefreshCalendarTokenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private GoogleAuthenticationService $googleAuthentication
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->googleAuthentication->refreshTokenIfNeeded();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
