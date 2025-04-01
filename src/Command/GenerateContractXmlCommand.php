<?php

namespace App\Command;

use App\Contract\HackyTemplateProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

#[AsCommand(
    name: 'app:generate-contract-xml',
    description: 'generate main part of docx as XML',
)]
class GenerateContractXmlCommand extends Command
{


    public function __construct(
        private Environment $twig,
        private KernelInterface $kernel,
        ?string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $docxPath = $io->ask('Please give the path of the contract docx (relative to the project).', 'assets/docx/contract_template.docx');

        $path = $io->ask('Please give the path you want your xml to be saved to (relative to the project) ?', 'templates/sonata/contract/contract_main_part_raw.xml.twig');

        $docxPath = $this->kernel->getProjectDir().'/'.$docxPath;

        $path = $this->kernel->getProjectDir().'/'.$path;

        $templateProcessor = new HackyTemplateProcessor($docxPath, $this->twig);

        $templateProcessor->generateMainPartXML($path);

        $io->success('XML successfully generated !');

        return Command::SUCCESS;
    }
}
