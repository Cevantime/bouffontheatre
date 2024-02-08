<?php

namespace App\Command;

use App\Scrapping\SourceScrapper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:scrap-booking',
    description: 'Add a short description for your command',
)]
class ScrapBookingCommand extends Command
{
    public function __construct(
        private SourceScrapper $sourceScrapper
    ) {
        parent::__construct('app:scrap-booking');
    }
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->sourceScrapper->scrapBookings();

        $io->success('Source scrapped');

        return Command::SUCCESS;
    }
}
