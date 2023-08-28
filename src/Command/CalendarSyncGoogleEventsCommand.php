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
        private EntityManagerInterface $manager,
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

        $i = 1;
        $nextPageToken = null;
        do {
            $json = $this->calendarService->getEvents(250, $nextPageToken);
            foreach ($json->items as $item) {
                $this->calendarService->persistGoogleEvent($item);
            }
            $this->manager->flush();
            $io->info('Saving page '.$i);
            $i++;
            $nextPageToken = $json->nextPageToken ?? null;
        } while($nextPageToken);

        $io->success('Events successfully fetched from Google');

        return Command::SUCCESS;
    }
}
