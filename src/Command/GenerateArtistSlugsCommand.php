<?php

namespace App\Command;

use App\Repository\ArtistRepository;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateArtistSlugsCommand extends Command
{
    protected static $defaultName = 'app:generate-artist-slugs';
    protected static $defaultDescription = 'Generate artists slugs';

    private ArtistRepository $artistRepository;
    private SlugService $artistSlugService;
    private EntityManagerInterface $entityManager;

    /**
     * @param ArtistRepository $artistRepository
     * @param ArtistSlugService $artistSlugService
     */
    public function __construct(ArtistRepository $artistRepository, SlugService $artistSlugService, EntityManagerInterface $entityManager)
    {
        $this->artistRepository = $artistRepository;
        $this->artistSlugService = $artistSlugService;
        $this->entityManager = $entityManager;
        parent::__construct(self::$defaultName);
        $this->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $artists = $this->artistRepository->findBy(['slug' => '']);

        foreach ($artists as $artist) {
            $this->artistSlugService->generateArtistSlug($artist);
            $this->entityManager->persist($artist);
        }
        $this->entityManager->flush();

        $io->success('Artist slugs generated !');

        return Command::SUCCESS;
    }
}
