<?php

namespace App\Command;

use App\Translation\CollectionFactoryObserverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use App\Translation\CollectionFactory;
use App\Translation\DomainCollection;

class StatusCommand extends Command implements CollectionFactoryObserverInterface
{
    protected static $defaultName = 'status';

    /** @var  OutputInterface */
    protected $output;

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument( 'path', InputArgument::REQUIRED, 'Where to search' )
            ->addArgument( 'name', InputArgument::REQUIRED, 'The name to match (please read https://symfony.com/doc/current/components/finder.html)' );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $path = $input->getArgument( 'path' );
        $name = $input->getArgument( 'name' );

        $output->writeln( sprintf( "<comment>Searching for : %s</comment>\n", $path . $name ) );
        $output->writeln( "" );

        $finder = new Finder();
        $finder->files()->in( $path )->name( $name );
        $collection = CollectionFactory::createFromFinder( $finder, $this );


        if ($collection->getDomains() == NULL) //$output->writeln( var_dump($collection) );
        {
            //$output->writeln( sprintf( "<comment>No domain found! Check the entered path and name are correct.</comment>\n") );
            //return;
            die("No domain found! Check the entered path and name are correct.\n");
        }

        $this->generateStatusFromCollection($collection, $output);
    }


    private function generateStatusFromCollection( DomainCollection $collection, OutputInterface $output )
    {
        foreach ( $collection->getDomains() as $domain )
        {
            $output->writeln( sprintf( "Domain '%s' has %s keys and supports : %s", $domain->getName(), count( $domain->getKeys() ), implode( $domain->getLocales(), ", " ) ) );
            if ( $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE)
            {
                $locales = $domain->getLocales();
                $keys = $domain->getKeys();
                foreach ( $keys as $key )
                {
                    $output->writeln( "<info>" . $key->getName() . '</info>' );
                    if ( $output->getVerbosity() >= OutputInterface::VERBOSITY_VERY_VERBOSE )
                    {
                        foreach ( $locales as $locale )
                        {
                            $output->writeln( sprintf( "%s: %s", $locale, $key->getTranslation( $locale )->getValue() ) );
                        }
                    }
                }
            }
        }
    }

    public function foundKeys( $keys, $filename )
    {
        $this->output->writeln( sprintf( "Found %s key in total in %s.", count( $keys ), $filename ) );
    }

    public function foundNewKeys( $keys, $filename )
    {
        if ( count( $keys ) == 0 )
        {
            $this->output->writeln( sprintf( "No new keys found in %s, great!", $filename ) );

            return;
        }
        $this->output->writeln( sprintf( "Found %s new keys.", count( $keys ) ) );
    }

    public function dealingWith( $source )
    {
        $this->output->writeln( sprintf( "Gathering translation keys in <info>%s</info>", $source ) );
    }
}
