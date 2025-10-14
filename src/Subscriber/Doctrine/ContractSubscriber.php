<?php

namespace App\Subscriber\Doctrine;

use App\Entity\Contract;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class ContractSubscriber
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $contract = $args->getObject();

        if (!($contract instanceof Contract)) {
            return;
        }

        $this->resetShares($contract);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $contract = $args->getObject();

        if (!($contract instanceof Contract)) {
            return;
        }

        $this->resetShares($contract);
    }

    private function resetShares(Contract $contract): void
    {
        if($contract->getContractType() !== Contract::TYPE_CO_PRODUCTION) {
            $contract->setShowMinimumShare(0);
            $contract->setShowTheaterShare(0);
            $contract->setShowCompanyShare(0);
        }
    }
}
