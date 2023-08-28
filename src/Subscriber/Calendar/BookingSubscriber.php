<?php

namespace App\Subscriber\Calendar;

use App\Calendar\Service\CalendarService;
use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BookingSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CalendarService $calendarService
    )
    {
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::preUpdate,
            Events::postRemove
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->syncEntityIfNeeded($args);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        if(count($args->getEntityChangeSet()) === 1 && isset($args->getEntityChangeSet()['googleId'])) {
            return;
        }
        $this->syncEntityIfNeeded($args);
    }

    private function syncEntityIfNeeded(LifecycleEventArgs $args)
    {
        if($args->getObject() instanceof Booking) {
            $this->calendarService->syncBooking($args->getObject());
        }
    }

    public function postRemove(PostRemoveEventArgs $args)
    {
        $entity = $args->getObject();
        if($entity instanceof Booking && $entity->getGoogleId()) {
            $this->calendarService->deleteGoogleEvent($entity->getGoogleId());
        }
    }
}
