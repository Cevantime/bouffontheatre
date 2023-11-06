<?php

namespace App\Subscriber\Doctrine;

use App\Entity\BlogPost;
use App\Service\SlugService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogPostSlugSubscriber implements EventSubscriberInterface
{
    private SlugService $slugService;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SlugService $slugger)
    {
        $this->slugService = $slugger;
    }

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
        $this->changeSlug($args, fn (BlogPost $post) => $post->getSlug() === null);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->changeSlug($args, fn (BlogPost $post) => $post->getSlug() === null);
    }

    private function changeSlug(LifecycleEventArgs $args, $condition)
    {
        $post = $args->getObject();
        if (!($post instanceof BlogPost)) {
            return;
        }
        if ($condition($post)) {
            $this->slugService->generatePostSlug($post);
        }
    }
}
