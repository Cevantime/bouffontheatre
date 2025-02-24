<?php

namespace App\Subscriber\Doctrine;

use App\Entity\MediaGallery;
use App\Entity\Project;
use App\Entity\Show;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

class ShowSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $show = $args->getObject();
        if (!($show instanceof Project)) {
            return;
        }
        if ($show->getOwner() === null) {
            $show->setOwner($this->security->getUser());
        }
        if($show->getGallery() === null) {
            $gallery = new MediaGallery();
            $gallery->setContext('default');
            $gallery->setName('Gallerie de ' . $show->getName());
            $show->setGallery($gallery);
            $args->getObjectManager()->persist($gallery);
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
