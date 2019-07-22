<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class ConvertCommand extends Command
{
    protected static $defaultName = 'convert';

    /** @var  OutputInterface */
    protected $output;

    protected function configure()
    {
        $this
            ->setDescription("Find document according to specific path.")
            ->addArgument('path', InputArgument::REQUIRED, 'Where to search')
            ->addArgument('name', InputArgument::REQUIRED, 'The name to match (please read https://symfony.com/doc/current/components/finder.html)')
            ->addArgument('output', InputArgument::REQUIRED, 'Where to store new generated files')
            ->addArgument('format', InputArgument::REQUIRED, 'The result of the conversion')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $format = $input->getArgument('format');
        $path = $input->getArgument('path');
        $name = $input->getArgument('name');
        $outputPath = $input->getArgument('output');

        if (in_array($format, ['yml', 'csv', 'yaml'])) {
            $output->writeln(sprintf("<comment>Searching for : %s</comment>\n", $path . $name));
            $output->writeln("");

            $finder = new Finder();
            $finder->files()->in($path)->name($name);

            $fileList = [];
            foreach ($finder as $file) {
                $fileList[] = $file->getRelativePathname(); //$fileList
            }
            $this->generateFile($fileList, $path, $outputPath, $format, $output);
        } else {
            die($output->writeln(sprintf("<comment>This conversion is not supported: %s</comment>", $format)));
        }
    }

    private function generateFile( $fileList, $inputPath, $outputPath, $convertTo, OutputInterface $output )
    {
        foreach ( $fileList as $file )
        {
            // let's split C:\wamp\www\market\app\Ressources\translations\messages.en.yml into [C:, wamp, ..., messages.en.yml]
            $tmp = explode( DIRECTORY_SEPARATOR, $file );
            // let's split messages.en.yml into [messages, en, yml]
            $f = explode( ".", $tmp[ count( $tmp ) - 1 ] );

            $ip = $inputPath . DIRECTORY_SEPARATOR . $file;
            $op = $outputPath . DIRECTORY_SEPARATOR . $f[ 0 ] . '.' . $f[ 1 ] . '.' . $convertTo;

            $function = 'create' . $convertTo . 'FileFrom' . $f[ 2 ];
            $this->$function( $ip, $op, $output );
        }
    }

    public static function flatten( $array, $prefix = '' )
    {
        $result = [];
        foreach ( $array as $key => $value )
        {
            if ( is_array( $value ) )
            {
                $result = $result + self::flatten( $value, $prefix . $key . '.' );
            }
            else
            {
                $result[ $prefix . $key ] = $value;
            }
        }

        return $result;
    }

    public function createCSVFileFromYML( $inputPath, $outputPath, OutputInterface $output )
    {
        if ( file_exists( $outputPath ) )
        {
            unlink( $outputPath );
        }
        $ymlContent = Yaml::parse( file_get_contents( $inputPath ) );
        $csvFile = fopen( $outputPath, 'x+' );
        ksort( $ymlContent, SORT_STRING | SORT_FLAG_CASE );

        $ymlContent = self::flatten( $ymlContent, '' );

        foreach ( $ymlContent as $key => $value )
        {
            fputcsv( $csvFile, [ $key, $value ], ';', '"' );
            /*
            $value = str_replace("\n", "\\n", $value); // To write '\n' and not interpret it
            $value = str_replace('"', '""', $value); // To not interpret double quotes in the csv
            $line = sprintf('%s;"%s"' . PHP_EOL, $key, $value); // Add quotes to not interpret semi-colon in $value
            fwrite($file, $line);

            if ( $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE )
            {
                $output->write($line);
            }
            */
        }
        fclose( $csvFile );
    }

    public function createYMLFileFromCSV( $inputPath, $outputPath, OutputInterface $output )
    {
        if ( file_exists( $outputPath ) )
        {
            unlink( $outputPath );
        }
        $dumper = new Dumper();
        $ymlFile = fopen( $outputPath, 'x+' );
        $csvFile = fopen( $inputPath, "r" );

        if ( $csvFile )
        {
            while ( ( $data = fgetcsv( $csvFile, 3000, ";" ) ) == TRUE )
            {
                $value = $dumper->dump( $data[ 1 ], 10 ); // To add appropriate quotes around string
                $line = $data[ 0 ] . ': ' . $value . PHP_EOL;
                fwrite( $ymlFile, $line );

                if ( $output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE )
                {
                    $output->write( $line );
                }
            }
        }
        fclose( $csvFile );
        fclose( $ymlFile );
    }
}
