<?php

namespace App\Command;

use Logic\FileFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class FileFinderCommand extends Command
{
    protected static $defaultName = 'find';

    protected function configure()
    {
        $this
            ->setDescription( "Find document according to specific path." )
            ->addArgument( 'path', InputArgument::REQUIRED, 'Where to search' )
            ->addArgument( 'name', InputArgument::REQUIRED, 'The name to match (please read https://symfony.com/doc/current/components/finder.html)' );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getArgument( 'path' );
        $name = $input->getArgument( 'name' );

        if ($path) {
            $io->note(sprintf('Searching in: %s', $path));
        }
        if ($name) {
            $io->note(sprintf('For files matching : %s', $name));
        }

        $output->writeln( sprintf( "<info>Searching for : %s</info>", $path . $name ) );
        $output->writeln( "" );

        $finder = new Finder();
        $finder->files()->in( $path )->name( $name );

        foreach ( $finder as $file )
        {
            $io->writeln('Found: '.$file->getFilename());
        }
    }
}
