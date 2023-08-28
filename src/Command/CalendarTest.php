<?php

namespace App\Command;

use App\Calendar\Service\CalendarService;
use App\Calendar\Service\GoogleAuthentication;
use App\Service\ConfigService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calendar:test',
    description: 'Add a short description for your command',
)]
class CalendarTest extends Command
{
    public function __construct(
        private CalendarService $calendarService,
        private GoogleAuthentication $googleAuthentication,
        string $name = null,
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->googleAuthentication->refreshTokenIfNeeded();

        $json = $this->calendarService->getEvents(10);
        dump($json->items);

        return Command::SUCCESS;
    }
}
