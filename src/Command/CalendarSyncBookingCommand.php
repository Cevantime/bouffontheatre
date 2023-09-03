<?php

namespace App\Command;

use App\Calendar\Service\CalendarService;
use App\Calendar\Service\GoogleAuthenticationService;
use App\Repository\BookingRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calendar:sync-booking',
    description: 'Add a short description for your command',
)]
class CalendarSyncBookingCommand extends Command
{
    public function __construct(
        private BookingRepository           $bookingRepository,
        private GoogleAuthenticationService $googleAuthentication,
        private CalendarService             $calendarService,
        string                              $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->googleAuthentication->refreshTokenIfNeeded();
        $unsyncedBooking = $this->bookingRepository->findBy(['googleId' => null]);
        $bookingCount = count($unsyncedBooking);
        $progressBar = $io->createProgressBar($bookingCount);
        $io->info('Syncing '.$bookingCount.' bookings');
        foreach ($unsyncedBooking as $booking) {
            $this->calendarService->syncBooking($booking);
            $progressBar->advance(1);
        }
        $io->success('Events were successfully synced !');

        return Command::SUCCESS;
    }
}
