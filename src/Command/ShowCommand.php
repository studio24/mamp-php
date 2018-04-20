<?php
declare(strict_types=1);

namespace Studio24\MampPHP\Command;

use Studio24\MampPHP\Service\PhpVersions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ShowCommand extends Command
{
    protected function configure()
    {
        $this->setName('show')
             ->setDescription('Shows the current PHP version used, and what versions are available via MAMP')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $php = new PhpVersions();

        $io = new SymfonyStyle($input, $output);

        $io->title('Current PHP version');
        $io->listing([$php->getCurrentVersion()]);

        $io->title('Available PHP versions in MAMP');
        $io->listing($php->getVersions());

        $io->text('To switch versions run:');
        $io->text('  mamp-php use [version number]');
    }

}