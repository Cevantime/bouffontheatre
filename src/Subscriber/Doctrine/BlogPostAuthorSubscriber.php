<?php

namespace App\Subscriber\Doctrine;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

class BlogPostAuthorSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $post = $args->getObject();
        if (!($post instanceof BlogPost)) {
            return;
        }
        $user = $this->security->getUser();
        if ($post->getAuthor() === null) {
            $post->setAuthor($this->security->getUser());
        }
    }

    /**
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist
        ];
    }
}
