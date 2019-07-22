<?php

namespace App\Command;

use App\Translation\CollectionFactory;
use App\Util\CollectionToYamlFileDumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class GenerateCommand extends Command
{
    protected static $defaultName = 'clean';

    /** @var  OutputInterface */
    protected $output;

    protected function configure()
    {
        $this
            ->setDescription( "Find translations and generate clean and complete files." )
            ->addArgument( 'path', InputArgument::REQUIRED, 'Where to search' )
            ->addArgument( 'name', InputArgument::REQUIRED, 'The name to match (please read https://symfony.com/doc/current/components/finder.html)' )
            ->addArgument( 'output', InputArgument::REQUIRED, 'Where to store new generated files' )
            ->addArgument( 'locales', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Specify required languages, so it will create file even if the domain is empty for that language.' );;
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $path = $input->getArgument( 'path' );
        $name = $input->getArgument( 'name' );
        $outputPath = $input->getArgument( 'output' );
        $requiredLanguages = $input->getArgument( 'locales' );

        $output->writeln( sprintf( "<comment>Searching for : %s</comment>\n", $path . $name ) );
        $output->writeln( "" );

        $finder = new Finder();
        $finder->files()->in($path)->name($name);
        $collection = CollectionFactory::createFromFinder( $finder );

        if ( $collection->getDomains() == NULL )
        {
            die( "No domain found! Check the entered path and name are correct.\n" );
        }

        CollectionToYamlFileDumper::collectionToFile($collection, $outputPath, $requiredLanguages);
    }

}
