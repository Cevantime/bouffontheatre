<?php

namespace App\Contract;

use App\Entity\ArtistItem;
use App\Entity\Contract;
use App\Entity\Show;
use App\Entity\User;
use App\Repository\ContractRepository;
use App\Service\ConfigService;
use App\Service\DTOService;
use App\Service\StringCallbacks;
use Twig\Extension\CoreExtension;

class ContractFactory
{
    public function __construct(
        private ConfigService      $configService,
        private DTOService         $DTOService,
        private ContractRepository $contractRepository,
    )
    {
    }

    public function createDefaultContract(Show $show = null)
    {
        $contract = new Contract();
//        $contractDate = new \DateTimeImmutable();
//        $contract->setContractDate($contractDate);
//        $contractSignatureDate = $contractDate->add(\DateInterval::createFromDateString('14 day'));
//        $contract->setContractSignatureDate($contractSignatureDate);

        $configs = $this->configService->getRawConfigs();
        $this->DTOService->transferDataTo($configs, $contract, StringCallbacks::class . '::camelize');
        if($show !== null) {
            $contract->setRelatedProject($show);
            $contract->setShowName($show->getName());
            $contract->setShowAuthor(CoreExtension::join($show->getAuthors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray()));
            $contract->setShowDirector(CoreExtension::join($show->getDirectors()->map(fn(ArtistItem $artistItem) => $artistItem->getArtist())->toArray()));
            $contract->setShowArtistCount($show->getActors()->count());
            $owner = $show->getOwner();
            if ($owner !== null) {
                $lastCompletedContract = $this->contractRepository->getUserLastCompletedContract($owner);
                if($lastCompletedContract) {
                    $this->DTOService->transferDataTo($lastCompletedContract, $contract, null, [
                        'companyName',
                        'companySiret',
                        'companyApe',
                        'companyLicense',
                        'companyPresident',
                        'companyAddress',
                        'companyAssurance',
                        'companyPhone',
                        'showRib'
                    ]);
                }
            }
        }
        return $contract;
    }
}
