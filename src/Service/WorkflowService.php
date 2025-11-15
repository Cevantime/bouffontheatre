<?php

namespace App\Service;

use App\DTO\Export;
use App\DTO\RevenueExport;
use App\Entity\Content;
use App\Entity\Contract;
use App\Entity\Performance;
use App\Entity\Period;
use App\Entity\PeriodItem;
use App\Entity\ProjectItem;
use App\Entity\Show;
use App\Entity\Workflow;
use App\Repository\ContentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class WorkflowService
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
        private ContentRepository         $contentRepository,
        private EntityManagerInterface    $entityManager,
        private KernelInterface           $kernel,
    )
    {
        IOFactory::registerWriter('Pdf', Mpdf::class);
    }

    public function workflowCanAccess(Workflow $workflow, string $section)
    {
        switch ($section) {
            case Workflow::STEP_BILLETREDUC:
            case Workflow::STEP_CONTRACT_CREATION:
                return $workflow->getAssociatedShow() !== null;
            case Workflow::STEP_FETCH_INFORMATIONS:
                return $this->workflowValidated($workflow, Workflow::STEP_CONTRACT_CREATION);
            case Workflow::STEP_HIGHTLIGHT:
            case Workflow::STEP_REMOVE:
                return $this->areShowInformationsFetched($workflow);
            case Workflow::STEP_SEND_CONTRACT:
                return $this->areInformationsFetched($workflow);
            case Workflow::STEP_SIGN_CONTRACT:
                return $this->workflowValidated($workflow, Workflow::STEP_SEND_CONTRACT);
            case Workflow::STEP_REVENUE_DECLARATION:
                return $workflow->getContract() != null && $workflow->getContract()->getStatus() === Contract::STATUS_SIGNED
                    && $workflow->getContract()->getPerformances()->last()->getPerformedAt() < new \DateTime();
            case Workflow::STEP_EMAILS:
            case Workflow::STEP_SIBIL:
            case Workflow::STEP_DECTANET:
            case Workflow::STEP_MANUAL:
                return $this->workflowValidated($workflow, Workflow::STEP_REVENUE_DECLARATION);

        }
        return false;
    }

    public function workflowValidated(Workflow $workflow, string $section)
    {
        switch ($section) {
            case Workflow::STEP_CONTRACT_CREATION:
                $contract = $workflow->getContract();
                if ($contract === null) {
                    return false;
                }
                if ($contract->getRelatedProject() !== $workflow->getAssociatedShow()) {
                    throw new \LogicException("Project associated with a workflow is different from the contract's project");
                }
                return !$contract->getPerformances()->isEmpty() || $this->isWorkflowContractFrozen($workflow);
            case Workflow::STEP_FETCH_INFORMATIONS:
                return $this->areInformationsFetched($workflow);
            case Workflow::STEP_SEND_CONTRACT:
                return $workflow->getContract() !== null && in_array($workflow->getContract()->getStatus(), [Contract::STATUS_SENT_TO_COMPANY, Contract::STATUS_SIGNED]);
            case Workflow::STEP_SIGN_CONTRACT:
                return $workflow->getContract()->getStatus() === Contract::STATUS_SIGNED;
            case Workflow::STEP_BILLETREDUC:
                return $this->checkBilletreduc($workflow);
            case Workflow::STEP_HIGHTLIGHT:
                return $workflow->isShowHighlighted();
            case Workflow::STEP_REMOVE:
                return $workflow->isShowRemoved();
            case Workflow::STEP_REVENUE_DECLARATION:
                return $this->isRevenueDeclarationDone($workflow);
            case Workflow::STEP_EMAILS:
                return $workflow->isRevenueEmailSentToPresident() && $workflow->isRevenueEmailSentToCompany();
            case Workflow::STEP_SIBIL:
                return $workflow->isSibilDone();
            case Workflow::STEP_DECTANET:
                return $workflow->isDectanetDone();
            case Workflow::STEP_MANUAL:
                return $workflow->isManualStepsDone();

        }

        return false;
    }

    public function isWorkflowContractFrozen(Workflow $workflow): bool
    {
        $contract = $workflow->getContract();
        return $contract !== null && in_array($contract->getStatus(), [Contract::STATUS_SIGNED, Contract::STATUS_SENT_TO_COMPANY]);
    }

    public function checkBilletreduc(Workflow $workflow): bool
    {
        return !$workflow->getAssociatedShow()->getShopLinks()->isEmpty();
    }

    public function highlight(Workflow $workflow): void
    {
        $show = $workflow->getAssociatedShow();
        $show->setBookable(true);
        $homeSlider = $this->contentRepository->getContentBySlug('home_slider');
        $homeShowsSlider = $this->contentRepository->getContentBySlug('home_shows');
        $footerProjects = $this->contentRepository->getContentBySlug('footer_projects');
        $showsGallery = $this->contentRepository->getContentBySlug('shows_gallery');
        $today = new \DateTime();
        $futurePredicate = function (Performance $performance) use ($today) {
            return $performance->getPerformedAt() > $today;
        };
        $futurePerformances = $workflow->getContract()->getPerformances()->filter($futurePredicate);

        $performedAtClosure = fn(Performance $performance) => $performance->getPerformedAt();

        $dates = $futurePerformances->map($performedAtClosure);
        $hasPlannedDates = !$dates->isEmpty();
        $nearestDate = $hasPlannedDates ? min($dates->toArray()) : null;

        foreach ([$homeShowsSlider, $homeSlider, $showsGallery, $footerProjects] as $content) {
            $newItem = new ProjectItem();
            $newItem->setProject($show);
            $newItem->setContent($content);
            $this->entityManager->persist($newItem);
            /** @var Content $content */
            $projectGallery = $content->getProjectGallery();
            $projects = $projectGallery->map(fn(ProjectItem $projectItem) => $projectItem->getProject());
            if ($projects->contains($show)) {
                $index = $projects->indexOf($show);
                $projectItem = $projectGallery->get($index);
                $this->removeFromCollection($projectGallery, $index);
                $this->entityManager->remove($projectItem);
            }

            if ($futurePerformances->isEmpty()) {
                $this->addToCollection($projectGallery, $newItem);
                continue;
            }
            for ($i = 0; $i < $projectGallery->count(); $i++) {
                $project = $projectGallery->get($i)->getProject();
                if (!($project instanceof Show)) {
                    $this->insertInCollection($projectGallery, $newItem, $i);
                    break;
                }
                $projectFuturePerformances = $project->getPerformances()->filter($futurePredicate);
                if ($projectFuturePerformances->isEmpty()) {
                    $this->insertInCollection($projectGallery, $newItem, $i);
                    break;
                }
                $projectNearestDate = min($projectFuturePerformances->map($performedAtClosure)->toArray());
                if ($projectNearestDate > $nearestDate) {
                    $this->insertInCollection($projectGallery, $newItem, $i);
                    break;
                }
            }
            if (!$projectGallery->map(fn(ProjectItem $projectItem) => $projectItem->getProject())->contains($show)) {
                $this->addToCollection($projectGallery, $newItem);
            }
        }

        $lastDate = $hasPlannedDates ? max($dates->toArray()) : null;

        if ($hasPlannedDates && $show->getSessions()->filter(fn(PeriodItem $periodItem) => $periodItem->getPeriod()->encloses($nearestDate, $lastDate))->isEmpty()) {
            $periodItem = new PeriodItem();
            $show->addSession($periodItem);
            $period = new Period();
            $periodItem->setPeriod($period);
            $period->setDateStart($nearestDate);
            $period->setDateEnd($lastDate);
            $period->setDays(array_unique($dates->map(fn(\DateTimeInterface $dateTime) => $dateTime->format('w'))->toArray()));
            $this->entityManager->persist($periodItem);
        }
        $workflow->setShowHighlighted(true);

        $this->entityManager->flush();

    }

    public function remove(Workflow $workflow)
    {
        $homeSlider = $this->contentRepository->getContentBySlug('home_slider');
        $homeShowsSlider = $this->contentRepository->getContentBySlug('home_shows');
        $footerProjects = $this->contentRepository->getContentBySlug('footer_projects');
        $show = $workflow->getAssociatedShow();
        foreach ([$homeSlider, $homeShowsSlider, $footerProjects] as $gallery) {
            $projects = $gallery->getProjectGallery()->map(fn(ProjectItem $projectItem) => $projectItem->getProject());
            while ($projects->contains($show)) {
                $index = $projects->indexOf($show);
                $this->entityManager->remove($gallery->getProjectGallery()->get($index));
                $this->removeFromCollection($gallery->getProjectGallery(), $index);
                $projects = $gallery->getProjectGallery()->map(fn(ProjectItem $projectItem) => $projectItem->getProject());
            }
        }

        $workflow->setShowRemoved(true);
        $this->entityManager->persist($workflow);
        $this->entityManager->flush();
    }

    private function addToCollection(Collection $collection, $element)
    {
        $element->setPosition($collection->count());
        $collection->add($element);
    }

    private function insertInCollection(Collection $collection, $element, $index)
    {
        for ($i = $collection->count() - 1; $i >= $index; $i--) {
            $item = $collection->get($i);
            $item->setPosition($i + 1);
            $collection->set($i + 1, $item);
        }

        $collection->set($index, $element);
        $element->setPosition($index);
    }

    private function removeFromCollection(Collection $collection, $index)
    {
        for ($i = $index + 1; $i < $collection->count(); $i++) {
            $pi = $collection->get($i);
            $pi->setPosition($pi->getPosition() - 1);
            $collection->set($i - 1, $pi);
        }
        $collection->remove($collection->count() - 1);
    }

    public function areInformationsFetched(Workflow $workflow)
    {
        if ($workflow->getContract() === null || $workflow->getAssociatedShow() === null) {
            return false;
        }
        $contractCompanyFields = [
            'companyName',
            'companySiret',
            'companyApe',
            'companyLicense',
            'companyPresident',
            'companyAddress',
            'companyAssurance',
            'companyPhone',
            'showRib',
        ];

        foreach ($contractCompanyFields as $companyField) {
            if ($this->propertyAccessor->getValue($workflow->getContract(), $companyField) === null) {
                return false;
            }
        }

        if (!$this->areShowInformationsFetched($workflow)) {
            return false;
        }

        return true;
    }

    public function areShowInformationsFetched(Workflow $workflow): bool
    {
        if ($workflow->getContract() === null || $workflow->getAssociatedShow() === null) {
            return false;
        }
        $contractShowFields = [
            'name',
            'punchline',
            'description',
            'banner',
        ];
        foreach ($contractShowFields as $contractShowField) {
            if ($this->propertyAccessor->getValue($workflow->getAssociatedShow(), $contractShowField) === null) {
                return false;
            }
        }

        $show = $workflow->getAssociatedShow();

        if ($show->getActors()->isEmpty()) {
            return false;
        }

        if ($show->getDirectors()->isEmpty()) {
            return false;
        }

        if ($show->getAuthors()->isEmpty()) {
            return false;
        }

        return true;
    }

    public function isRevenueDeclarationDone(Workflow $workflow)
    {
        foreach ($workflow->getContract()->getPerformances() as $performance) {
            foreach (['grossRevenue', 'fullPriceCount', 'halfPriceCount'] as $field) {
                if ($this->propertyAccessor->getValue($performance, $field) === null) {
                    return false;
                }
            }
        }
        foreach (['copyrightApplicable', 'retirementContribApplicable', 'agessaContribApplicable', 'revenueTickBossPdf'] as $field) {
            if ($this->propertyAccessor->getValue($workflow, $field) === null) {
                return false;
            }
        }

        return true;
    }

    public function prefillPerformancesWithRevenueExcel(Workflow $workflow, UploadedFile $excelFile)
    {
        $contract = $workflow->getContract();
        $reader = IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($excelFile->getRealPath());

        $activeSheet = $spreadsheet->getActiveSheet();

        $performances = $contract->getPerformances();

        $performanceDates = $performances->map(function (Performance $performance) {
            return $performance->getPerformedAt()->format('d/m/Y');
        });

        $prices = [
            'fullPrice' => intval($contract->getShowFullPrice() ?: 17),
            'halfPrice' => intval($contract->getShowHalfPrice() ?: 12),
            'taxFreePrice' => intval($contract->getShowTaxFreePrice() ?: 8),
            'appPrice' => intval($contract->getShowAppPrice() ?: 10),
            'free' => 0
        ];

        $mapPrices = array_flip($prices);

        for ($j = 1; $j < $activeSheet->getHighestDataRow(); $j++) {
            if (!$activeSheet->cellExists([1, $j])) {
                break;
            }
            $date = $activeSheet->getCell([1, $j])->getFormattedValue();
            $performanceIndex = $performanceDates->indexOf($date);
            if ($performanceIndex === false) {
                continue;
            }
            $performance = $performances->get($performanceIndex);
            foreach ($mapPrices as $price) {
                $this->propertyAccessor->setValue($performance, $price . 'Count', 0);
            }
            $priceRow = $j + 3;
            for ($i = 1; $i < $activeSheet->getHighestColumn(); $i++) {
                if (!$activeSheet->cellExists([$i, $priceRow])) {
                    break;
                }
                $cellPriceValue = $activeSheet->getCell([$i, $priceRow])->getValue();
                if (array_key_exists($cellPriceValue, $mapPrices)) {
                    $cellCountValue = $activeSheet->getCell([$i, $priceRow + 1]);
                    $currentValue = $this->propertyAccessor->getValue($performance, $mapPrices[$cellPriceValue] . 'Count');
                    $this->propertyAccessor->setValue($performance, $mapPrices[$cellPriceValue] . 'Count', $currentValue + ($cellCountValue->getValue() ?: 0));
                } else if ($cellPriceValue === 'Total TTC') {
                    $cellCountValue = $activeSheet->getCell([$i, $priceRow + 1]);
                    $this->propertyAccessor->setValue($performance, 'grossRevenue', $cellCountValue->getValue());
                }
            }
        }
        $this->entityManager->flush();

    }

    public function generateRevenueExport(Workflow $workflow): Export
    {
        $reader = IOFactory::createReader("Xlsx");

        $spreadsheet = $reader->load($this->kernel->getProjectDir() . '/assets/ods/global_revenue_template.xlsx');
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValue('B2', $workflow->getAssociatedShow()->getName());
        $worksheet->setCellValue('B3', $workflow->getContract()->getCompanyName());
        $performances = $workflow->getContract()->getPerformances();
        $worksheet->setCellValue('F3', $workflow->getContract()->getCompanyLicense());
        $worksheet->getCell('D5')->setValueExplicit($performances->first()->getPerformedAt()->format('m/d/Y'), DataType::TYPE_ISO_DATE);
        $worksheet->getCell('F5')->setValueExplicit($performances->last()->getPerformedAt()->format('m/d/Y'), DataType::TYPE_ISO_DATE);

        $firstPerformance = $performances->get(0);
        $worksheet->setCellValue('B9', $firstPerformance->getPerformedAt()->format('d/m/Y'));
        $worksheet->setCellValue('C9', $firstPerformance->getGrossRevenue());
        $worksheet->setCellValue('E9', $firstPerformance->getFullPriceCount() + $firstPerformance->getHalfPriceCount() + $firstPerformance->getAppPriceCount() + $firstPerformance->getTaxFreePriceCount());
        $worksheet->setCellValue('F9', $firstPerformance->getFreeCount());

        for ($i = 1; $i < $performances->count(); $i++) {
            $p = $performances->get($i);
            $this->duplicateRowWithValues($worksheet, 8 + $i, [
                'B' => $p->getPerformedAt()->format('d/m/Y'),
                'C' => $p->getGrossRevenue(),
                'E' => $p->getFullPriceCount() + $p->getHalfPriceCount() + $p->getAppPriceCount() + $p->getTaxFreePriceCount(),
                'F' => $p->getFreeCount()
            ]);
        }

        $pCount = $performances->count();

        $spreadsheet->getNamedRange('RECETTES')->setRange(sprintf('$C$9:$C$%s', 8 + $pCount));
        $spreadsheet->getNamedRange('CONTRIBUTION_DIFFUSEUR_AGESSA')->setRange(sprintf('$C$%s', 14 + $pCount));
        $spreadsheet->getNamedRange('NOMBRE_REPRESENTATIONS')->setRange(sprintf('$A$9:$A$%s', 8 + $pCount));
        $spreadsheet->getNamedRange('CONTRIBUTION_RETRAITE')->setRange(sprintf('$C$%s', 13 + $pCount));
        $spreadsheet->getNamedRange('DROIT_D_AUTEUR')->setRange(sprintf('$C$%s', 12 + $pCount));
        $spreadsheet->getNamedRange('RECETTE_BRUTE')->setRange(sprintf('$C$%s', 9 + $pCount));
        $spreadsheet->getNamedRange('RECETTE_HTVA')->setRange(sprintf('$C$%s', 11 + $pCount));
        $spreadsheet->getNamedRange('TAXE_PARAFISCALE')->setRange(sprintf('$C$%s', 15 + $pCount));
        $spreadsheet->getNamedRange('TVA')->setRange(sprintf('$C$%s', 10 + $pCount));
        $spreadsheet->getNamedRange('PART_THEATRE')->setRange(sprintf('$F$%s', 14 + $pCount));
        $spreadsheet->getNamedRange('PART_COMPAGNIE')->setRange(sprintf('$F$%s', 15 + $pCount));
//        $spreadsheet->getNamedRange('LIGNE_APPLICABLE')->setRange(sprintf('$B$%s:$B$%s', 12 + $pCount, 14 + $pCount));
        $spreadsheet->getNamedRange('RECETTE_NETTE')->setRange(sprintf('$C$%s', 16 + $pCount));
        $spreadsheet->getNamedRange('MINIMUM_GARANTI_THEATRE_PAR_REPRESENTATION')->setRange(sprintf('$G$%s', 9 + $pCount));
        $spreadsheet->getNamedRange('MINIMUM_GARANTI_TOTAL')->setRange(sprintf('$G$%s', 10 + $pCount));
        $spreadsheet->getNamedRange('MINIMUM_COMPAGNIE_PAR_REPRESENTATION')->setRange(sprintf('$G$%s', 11 + $pCount));
        $spreadsheet->getNamedRange('DIFFERENCE_RECETTE_MINIMUM_GARANTI')->setRange(sprintf('$G$%s', 12 + $pCount));
        $spreadsheet->getNamedRange('TVA_DIFFERENCE_MINIMUM_GARANTI')->setRange(sprintf('$G$%s', 13 + $pCount));
        $spreadsheet->getNamedRange('TOTAL_THEATRE')->setRange(sprintf('$G$%s', 14 + $pCount));

        $theaterMinimumShareCell = $worksheet->getCell(sprintf('G%s', 9 + $pCount));
        $theaterMinimumShareCell->setValue($workflow->getContract()->getShowTheaterShare());

        $companyMinimumShareCell = $worksheet->getCell(sprintf('G%s', 11 + $pCount));
        $companyMinimumShareCell->setValue($workflow->getContract()->getShowCompanyShare());


        $theaterShareCell = $worksheet->getCell(sprintf('F%s', 14 + $pCount));
        $theaterShareCell->setValue($workflow->getContract()->getShowTheaterSharePercent() / 100.00);

        $companyShareCell = $worksheet->getCell(sprintf('F%s', 15 + $pCount));
        $companyShareCell->setValue($workflow->getContract()->getShowCompanySharePercent() / 100.00);

        $copyrightApplicableCell = $worksheet->getCell(sprintf('B%s', 12 + $pCount));
        $retirementApplicableCell = $worksheet->getCell(sprintf('B%s', 13 + $pCount));
        $contribAgessaApplicableCell = $worksheet->getCell(sprintf('B%s', 14 + $pCount));

        $copyrightApplicableCell->setValueExplicit($workflow->isCopyrightApplicable() ? 'Applicable' : 'Non applicable');
        $retirementApplicableCell->setValueExplicit($workflow->isRetirementContribApplicable() ? 'Applicable' : 'Non applicable');
        $contribAgessaApplicableCell->setValueExplicit($workflow->isAgessaContribApplicable() ? 'Applicable' : 'Non applicable');

        $worksheet->getCell(sprintf('C%s', 12 + $pCount))->updateInCollection();
        $worksheet->getCell(sprintf('C%s', 13 + $pCount))->updateInCollection();
        $worksheet->getCell(sprintf('C%s', 14 + $pCount))->updateInCollection();

        $worksheet->setCellValue(sprintf('A%s', 18 + $pCount), sprintf("BILAN FAIT A PARIS EN DEUX EXEMPLAIRES LE %s", (new \DateTime())->format('d/m/y')));

        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");

        $path = tempnam('/tmp', 'revenue_report_');

        $writer->save($path);

        return new RevenueExport($path, sprintf('Recettes globales %s.xlsx', $workflow->getAssociatedShow()), $worksheet->getCell(sprintf('G%s', 15 + $pCount))->getFormattedValue());
    }

    private function duplicateRowWithValues(Worksheet $sheet, int $sourceRow, array $customValues = []): void
    {
        $sheet->insertNewRowBefore($sourceRow + 1, 1);
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        // Copy cell values and styles from source to target row
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $targetCell = $colLetter . ($sourceRow + 1);
            // Copy value
            $fromCell = $sheet->getCell([$col, $sourceRow]);
            $toCell = $sheet->getCell([$col, $sourceRow + 1]);
            $toCell->setXfIndex($fromCell->getXfIndex());
            if ($fromCell->isFormula()) {
                $originalFormula = $fromCell->getValue();
                $updatedFormula = $this->adjustFormulaRowReferences($originalFormula, $sourceRow, $sourceRow + 1);
                $sheet->setCellValue($targetCell, $updatedFormula);
            } else {
                $sheet->setCellValue($targetCell, $fromCell->getValue());
            }

        }

        // Inject custom values
        foreach ($customValues as $colLetter => $value) {
            $sheet->setCellValue($colLetter . ($sourceRow + 1), $value);
        }
    }

    private function adjustFormulaRowReferences(string $formula, int $sourceRow, int $targetRow): string
    {
        // Match cell references like A5, B$3, $C6, $D$7
        return preg_replace_callback('/([$]?)([A-Z]{1,3})([$]?)(\d+)/', function ($matches) use ($sourceRow, $targetRow) {
            [$full, $colFixed, $col, $rowFixed, $row] = $matches;

            // If the row is relative (no $), update it
            if ($rowFixed === '') {
                $newRow = $targetRow + ((int)$row - $sourceRow);
                return "{$colFixed}{$col}{$rowFixed}{$newRow}";
            }

            // If it's an absolute row ($5), keep it as-is
            return $full;
        }, $formula);
    }

}
