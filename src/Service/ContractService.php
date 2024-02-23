<?php

namespace App\Service;

use App\Entity\Contract;
use App\Entity\Performance;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\String\u;

class ContractService
{

    public function __construct(
        private KernelInterface           $kernel,
        private PropertyAccessorInterface $propertyAccessor,
        private TranslatorInterface       $translator
    )
    {
    }

    private function setRawContractValue(Contract $contract, TemplateProcessor $templateProcessor, $key)
    {
        $templateProcessor->setValue($key, $this->propertyAccessor->getValue($contract, $key));
    }

    private function setRawContractValues(Contract $contract, TemplateProcessor $templateProcessor, $keys)
    {
        foreach ($keys as $key) {
            $this->setRawContractValue($contract, $templateProcessor, $key);
        }
    }

    public function createGeneratedContractResponse(Contract $contract)
    {
        $export = $this->generateContractFile($contract);
        $response = new BinaryFileResponse($export['path']);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $export['name']
        );
        return $response;
    }

    public function generateContractFile(Contract $contract): array
    {

        $templateProcessor = new TemplateProcessor($this->kernel->getProjectDir() . "/assets/docx/contract_template.docx");

        $this->setRawContractValues($contract, $templateProcessor, [
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
            'contractCity',
            'tva',
        ]);
        $perfCount = $contract->getPerformances()->count();
        $templateProcessor->setValue('showCount', $contract->getPerformances()->count());
        $self = $this;

        $templateProcessor->cloneBlock('showDates', 0, true, false, array_map(
            function (Performance $p) use ($self) {
                return ["showDate" => $self->formatDate($p->getPerformedAt())];
            },
            $contract->getPerformances()->toArray()
        ));
        $templateProcessor->setValue('showTheaterShare', $perfCount * $contract->getShowTheaterShare());
        $templateProcessor->setValue('showCompanyShare', $perfCount * $contract->getShowCompanyShare());
        $templateProcessor->setValue('contractDate', $this->formatSimpleDate($contract->getContractDate()));
        $templateProcessor->setValue('contractSignatureDate', $this->formatSimpleDate($contract->getContractSignatureDate()));

        $exportName = 'contrat_'.u($contract->getShowName())->snake()->lower().'_'.($contract->getContractDate()->format('d_m_y')).'.docx';
        $dir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'bouffon_contract'.DIRECTORY_SEPARATOR;
        if( ! file_exists($dir)) {
            mkdir($dir);
        }
        $path = $dir.$exportName;
        $templateProcessor->saveAs($path);
        return [
            'path' => $path,
            'name' => $exportName
        ];
    }

    private function formatDate(\DateTimeInterface $dateTime)
    {
        return sprintf("%s %s %s %s à %sh",
            $this->translator->trans($dateTime->format('l')),
            $dateTime->format('d'),
            $this->translator->trans($dateTime->format('F')),
            $dateTime->format('Y'),
            $dateTime->format('H')
        );
    }

    private function formatSimpleDate(\DateTimeInterface $dateTime)
    {
        return sprintf("%s %s %s %s",
            $this->translator->trans($dateTime->format('l')),
            $dateTime->format('d'),
            $this->translator->trans($dateTime->format('F')),
            $dateTime->format('Y')
        );
    }
}
