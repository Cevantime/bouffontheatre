<?php

namespace App\Subscriber\Doctrine;

use App\Entity\Show;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

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
        if(!($show instanceof Show)) {
            return;
        }
        if($show->getOwner() === null) {
            $show->setOwner($this->security->getUser());
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
