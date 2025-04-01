<?php

namespace App\Subscriber\Doctrine;

use App\Entity\BlogPost;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class BlogPostDateSubscriber
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

    public function prePersist(PrePersistEventArgs $args)
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
