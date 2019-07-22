<?php

namespace App\Command;

use App\Translation\CollectionFactory;
use App\Translation\Domain;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class DebugCommand extends Command
{
    protected static $defaultName = 'debug';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('path', InputArgument::REQUIRED, 'Where to search')
            ->addArgument('name', InputArgument::REQUIRED, 'The name to match (please read https://symfony.com/doc/current/components/finder.html)')
            ->addArgument('locale', InputArgument::REQUIRED, 'locale')
            ->addArgument('domain', InputArgument::REQUIRED, 'domain');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->output = $output;
        $path = $input->getArgument('path');
        $name = $input->getArgument('name');
        $locale = $input->getArgument('locale');
        $domain = $input->getArgument('domain');

        $io->comment(sprintf("Searching for : %s", $path . $name));

        $finder = new Finder();
        $finder->files()->in($path)->name($name);
        $collection = CollectionFactory::createFromFinder($finder);

        $io->title("Domains");
        foreach ($collection->getDomains() as $_domain) {
            $io->text("* ".$_domain->getName());
        }

        $io->title("Translations");
        if ($collection->getDomains() == NULL) //$output->writeln( var_dump($collection) );
        {
            //$output->writeln( sprintf( "<comment>No domain found! Check the entered path and name are correct.</comment>\n") );
            //return;
            $output->writeln("<error>No domain found! Check the entered path and name are correct.</error>");
        }

        $table = new Table($output);
        $table->setHeaders(['key', 'value']);
        $table->setRows($this->convertAssocToRow($this->convertDomainToArray($collection->getDomain($domain), $locale)));
        $table->render();
    }

    protected function convertDomainToArray(Domain $domain, $locale)
    {
        $array = [];
        $keys = $domain->getKeys();

        foreach ($keys as $key) {
            $translation = $key->getTranslation($locale)->getValue();
            $array[$key->getName()] = $translation;
        }
        return $array;
    }

    private function convertAssocToRow(array $convertDomainToArray)
    {
        $rows = [];
        foreach ($convertDomainToArray as $key => $value) {
            $rows[] = [sprintf('"%s"', $key), $value];
        }
        return $rows;
    }
}
