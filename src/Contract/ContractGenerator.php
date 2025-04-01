<?php

namespace App\Contract;

use App\DTO\Export;
use App\Entity\Contract;
use App\Entity\Performance;
use App\Service\ConfigService;
use App\Service\DTOService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use function Symfony\Component\String\u;

class ContractGenerator
{

    public function __construct(
        private KernelInterface           $kernel,
        private PropertyAccessorInterface $propertyAccessor,
        private Environment               $twig,
        private TranslatorInterface       $translator,
        private ConfigService             $configService,
        private DTOService                $DTOService,
    )
    {
    }


    private function setRawContractValue(Contract $contract, array &$twigContext, $key)
    {
        $twigContext[$key] = $this->propertyAccessor->getValue($contract, $key);
    }

    private function setRawContractValues(Contract $contract, array &$twigContext, $keys)
    {
        foreach ($keys as $key) {
            $this->setRawContractValue($contract, $twigContext, $key);
        }
    }

    public function createGeneratedContractResponse(Contract $contract)
    {
        $export = $this->generateContractFile($contract);
        $response = new BinaryFileResponse($export->path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $export->name
        );
        return $response;
    }

    public function generateContractFile(Contract $contract): Export
    {

        $templateProcessor = new HackyTemplateProcessor($this->kernel->getProjectDir() . "/assets/docx/contract_template.docx", $this->twig);
        $twigContext = [];
        $this->setRawContractValues($contract, $twigContext, [
            'theaterAddress',
            'theaterSiret',
            'theaterAddress',
            'theaterSiret',
            'theaterPresident',
            'theaterEmail',
            'theaterPhone',
            'companyName',
            'companySiret',
            'companyApe',
            'companyLicense',
            'companyPresident',
            'companyAddress',
            'companyAssurance',
            'companyPhone',
            'showName',
            'showAuthor',
            'showDirector',
            'theaterName',
            'showServiceSession',
            'showArtistCount',
            'showDuration',
            'showTheaterAvailability',
            'theaterBookingPhone',
            'showFullPrice',
            'showHalfPrice',
            'showMaxDuration',
            'showInvitations',
            'showCompanySharePercent',
            'showTheaterSharePercent',
            'showMinimumShare',
            'showRib',
            'rentPrice',
            'contractCity',
            'tva',
            'stageManagementInstallHourCount',
            'stageManagementShowHourCount',
            'stageManagementInstallPrice',
            'stageManagementShowPrice',
            'contractType',
            'minimumShare'
        ]);
        $perfCount = $contract->getPerformances()->count();
        $twigContext['showCount'] = $contract->getPerformances()->count();
        $self = $this;

        $twigContext['showDates'] = array_map(
            function (Performance $p) use ($self) {
                return $self->formatDate($p->getPerformedAt());
            },
            $contract->getPerformances()->toArray()
        );


        $twigContext['showTheaterShare'] = $perfCount * $contract->getShowTheaterShare();
        $twigContext['showCompanyShare'] = $perfCount * $contract->getShowCompanyShare();
        $twigContext['contractDate'] = $this->formatSimpleDate($contract->getContractDate());
        $twigContext['contractSignatureDate'] = $this->formatSimpleDate($contract->getContractSignatureDate());

        $exportName = 'contrat_' . u($contract->getShowName())->snake()->lower() . '_' . ($contract->getContractDate()->format('d_m_y')) . '.docx';
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bouffon_contract';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $path = $dir . DIRECTORY_SEPARATOR . $exportName;
        $i = 0;
//        $templateProcessor->saveAsWithTwigMainPart($path, 'sonata/contract/contract_main_part.xml.twig', $twigContext);
        $templateProcessor->saveAsWithTwigMainPart($path, 'sonata/contract/contract_main_part.xml.twig', $twigContext);

        return new Export($path, $exportName);
    }

    private function formatDate(\DateTimeInterface $dateTime)
    {
        return sprintf(
            "%s %s %s %s à %sh",
            $this->translator->trans($dateTime->format('l')),
            $dateTime->format('d'),
            $this->translator->trans($dateTime->format('F')),
            $dateTime->format('Y'),
            $dateTime->format('H')
        );
    }

    private function formatSimpleDate(\DateTimeInterface $dateTime)
    {
        return sprintf(
            "%s %s %s %s",
            $this->translator->trans($dateTime->format('l')),
            $dateTime->format('d'),
            $this->translator->trans($dateTime->format('F')),
            $dateTime->format('Y')
        );
    }
}
