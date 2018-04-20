<?php
declare(strict_types=1);

namespace Studio24\MampPHP\Command;

use Studio24\MampPHP\Service\PhpVersions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class UseCommand extends Command
{
    protected function configure()
    {
        $this->setName('use')
             ->setDescription('Set the version of PHP for MAMP to use on the command line')
             ->addArgument('version', InputArgument::REQUIRED, 'What version of PHP do you want to use on the command line?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $input->getArgument('version');
        $php = new PhpVersions();
        $io = new SymfonyStyle($input, $output);

        $current = $php->getCurrentVersion();
        if ($version === $current) {
            $io->error("PHP version is already set to $version, nothing to do!");
            return;
        }

        $io->title('Switch to PHP version: ' . $version);

        $php->setupBashProfile();
        $php->switchPhp($version);

        $current = $php->getCurrentVersion();
        if ($version === $current) {
            $io->success("PHP version switched to $version");
        } else {
            $io->error("PHP version cannot be switched (current version $current)!");
        }
    }
    
}