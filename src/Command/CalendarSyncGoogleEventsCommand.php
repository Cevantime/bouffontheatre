<?php

namespace App\Command;

use App\Calendar\Service\CalendarService;
use App\Calendar\Service\GoogleAuthentication;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calendar:sync-google-events',
    description: 'Add a short description for your command',
)]
class CalendarSyncGoogleEventsCommand extends Command
{
    public function __construct(
        private GoogleAuthentication $googleAuthentication,
        private CalendarService $calendarService,
        string $name = null
    )
    {
        parent::__construct(
            $name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->googleAuthentication->refreshTokenIfNeeded();
        $io = new SymfonyStyle($input, $output);

       $this->calendarService->syncEvents();

        $io->success('Events successfully fetched from Google');

        return Command::SUCCESS;
    }
}
