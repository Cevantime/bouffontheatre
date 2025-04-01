<?php

namespace App\Subscriber\Doctrine;

use App\Entity\Link;
use App\Service\LinkSiteNameExtractor;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class LinkSiteNameSubscriber
{
    private LinkSiteNameExtractor $linkSiteNameExtractor;

    /**
     * @param LinkSiteNameExtractor $linkSiteNameExtractor
     */
    public function __construct(LinkSiteNameExtractor $linkSiteNameExtractor)
    {
        $this->linkSiteNameExtractor = $linkSiteNameExtractor;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->updateSiteName($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->updateSiteName($args);
    }

    private function updateSiteName(LifecycleEventArgs $args)
    {
        $link = $args->getObject();

        if(!($link instanceof Link)) {
            return;
        }

        $link->setSiteName($this->linkSiteNameExtractor->getSiteName($link->getUrl()));
    }
}
