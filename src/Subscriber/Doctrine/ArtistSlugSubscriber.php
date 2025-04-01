<?php

namespace App\Subscriber\Doctrine;

use App\Entity\Artist;
use App\Service\SlugService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class ArtistSlugSubscriber
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
        $this->changeSlug($args, fn (Artist $artist) => $artist->getSlug() === null);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->changeSlug(
            $args,
            function ($artist) use ($args) {
                return $artist->getSlug()  === null || (!empty(array_intersect(['firstname', 'lastname'], array_keys($args->getEntityChangeSet())) && !in_array('slug', $args->getEntityChangeSet())));
            }
        );
    }

    private function changeSlug(LifecycleEventArgs $args, $condition)
    {
        $artist = $args->getObject();
        if (!($artist instanceof Artist)) {
            return;
        }
        if ($condition($artist)) {
            $this->slugService->generateArtistSlug($artist);
        }
    }
}
