<?php

namespace App\Subscriber\Calendar;

use App\Calendar\Service\CalendarService;
use App\Service\ConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SyncEventsSubscriber implements EventSubscriberInterface
{
    const TRIGGER_ROUTES = [
        'fc_load_events',
        'app_calendar'
    ];

    const INTERVAL_SYNC_SECONDS = 15;

    public function __construct(
        private ConfigService $config,
        private CalendarService $calendarService
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');
        if( ! in_array($route, self::TRIGGER_ROUTES)) {
            return;
        }
        $mustSync = !$this->config->hasValue(ConfigService::EVENT_LAST_SYNC_TIME)
            || $this->config->getValue(ConfigService::EVENT_LAST_SYNC_TIME) + self::INTERVAL_SYNC_SECONDS > time();
        if($mustSync) {
            $this->calendarService->syncEvents();
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
