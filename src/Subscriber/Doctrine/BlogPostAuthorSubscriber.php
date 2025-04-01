<?php

namespace App\Subscriber\Doctrine;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist)]
class BlogPostAuthorSubscriber
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
