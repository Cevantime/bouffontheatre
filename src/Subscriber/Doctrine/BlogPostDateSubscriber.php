<?php

namespace App\Subscriber\Doctrine;

use App\Entity\BlogPost;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

class BlogPostDateSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $post = $args->getObject();
        if (!($post instanceof BlogPost)) {
            return;
        }
        $post->setCreatedAt(new DateTimeImmutable());
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $post = $args->getObject();
        if (!($post instanceof BlogPost)) {
            return;
        }

        $post->setUpdatedAt(new DateTimeImmutable());
    }
}
