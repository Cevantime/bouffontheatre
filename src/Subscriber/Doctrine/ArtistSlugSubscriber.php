<?php

namespace App\Subscriber\Doctrine;

use App\Entity\Artist;
use App\Service\ArtistSlugService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArtistSlugSubscriber implements EventSubscriberInterface
{
    private ArtistSlugService $slugService;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(ArtistSlugService $slugger)
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
        $this->changeSlug($args, fn(Artist $artist) => $artist->getSlug() === null);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->changeSlug(
            $args,
            function($artist) use ($args) {
                return $artist->getSlug()  === null || (!empty(array_intersect(['firstname', 'lastname'], array_keys($args->getEntityChangeSet())) && ! in_array('slug', $args->getEntityChangeSet())));
            }
        );
    }

    private function changeSlug(LifecycleEventArgs $args, $condition)
    {
        $artist = $args->getObject();
        if(!($artist instanceof Artist)) {
            return;
        }
        if($condition($artist)) {
            $this->slugService->generateArtistSlug($artist);
        }
    }
}
