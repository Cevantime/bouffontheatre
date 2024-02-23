<?php

namespace App\Command;

use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:test-template',
    description: 'Test docx templates',
)]
class TestTemplateCommand extends Command
{
    public function __construct(
        private KernelInterface $kernel
    ) {
        parent::__construct('app:test-template');
    }
    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $templateProcessor = new TemplateProcessor($this->kernel->getProjectDir() . "/assets/docx/contract_template.docx");

        $templateProcessor->setValue('theaterAddress', "26/28 rue de Meaux 75019 Paris");
        $templateProcessor->setValue('theaterSiret', "784 779 357 002 26");
        $templateProcessor->setValue('theaterPresident', "RICHARD ARSELIN");
        $templateProcessor->setValue('theaterEmail', "contactbouffon@gmail.com");
        $templateProcessor->setValue('theaterPhone', "01 42 38 35 53");
        $templateProcessor->setValue('companyName', 'Boss Kapok');
        $templateProcessor->setValue('companySiret', "123 456 789");
        $templateProcessor->setValue('companyApe', "9001Z");
        $templateProcessor->setValue('companyLicense', "platesv-R-2020-007899");
        $templateProcessor->setValue('companyPresident', "Jules Romain");
        $templateProcessor->setValue('companyAddress', "20 rue du quai");
        $templateProcessor->setValue('companyAssurance', "John Lennon");
        $templateProcessor->setValue('companyPhone', "0123456789");
        $templateProcessor->setValue('showName', "Andromaque et moi");
        $templateProcessor->setValue('showAuthor', "Racinezedark");
        $templateProcessor->setValue('showDirector', "Steven Spielberg");
        $templateProcessor->setValue('theaterName', "L'association Bouffon Theatre");
        $templateProcessor->setValue('showServiceSession', "le mardi 25 février de 14h à 18h");
        $templateProcessor->setValue('showArtistCount', "5");
        $templateProcessor->setValue('showDuration', "1h10");
        $templateProcessor->setValue('showCount', "4");
        $templateProcessor->cloneBlock('showDates', 0, true, false, [
            ["showDate" => 'Samedi 27 décembre 2001 à 21h'],
            ["showDate" => 'Samedi 28 décembre 2011 à 21'],
            ["showDate" => 'Samedi 30 décembre 2021 à 21h'],
            ["showDate" => 'Samedi 31 décembre 2023 à 19h'],
        ]);
        $templateProcessor->setValue('showTheaterAvailability', "à 20h15");
        $templateProcessor->setValue('theaterBookingPhone', "01 42 38 35 53");
        $templateProcessor->setValue('showFullPrice', "17");
        $templateProcessor->setValue('showHalfPrice', "12");
        $templateProcessor->setValue('showMaxDuration', "1h20");
        $templateProcessor->setValue('showInvitations', "Les invitations sont strictement limitées aux professionnels : Journalistes et Programmateurs.");
        $templateProcessor->setValue('showTheaterShare', "100");
        $templateProcessor->setValue('showCompanyShare', "100");
        $templateProcessor->setValue('showCompanySharePercent', "50");
        $templateProcessor->setValue('showTheaterSharePercent', "50");
        $templateProcessor->setValue('showMinimumShare', "100");
        $templateProcessor->setValue('showRib', "MON SUPER RIB");
        $templateProcessor->setValue('contractCity', "Paris");
        $templateProcessor->setValue('contractDate', "19 février 2024");
        $templateProcessor->setValue('contractSignatureDate', "3 mars 2024");
        $templateProcessor->setValue('tva', "20");

        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        $templateProcessor->saveAs("generated.docx");

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
