<?php

namespace App\Command;

use App\Translation\DomainCollection;
use App\Util\CollectionToYamlFileDumper;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class XlsxYamlCommand extends Command
{
    protected static $defaultName = 'xlsx:yaml';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('xlsx', InputArgument::REQUIRED, 'Where to search')
            ->addArgument('output', InputArgument::REQUIRED, 'Where to store new generated files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $xlsx = $input->getArgument('xlsx');
        $outputPath = $input->getArgument('output');

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($xlsx);
        } catch (\Exception $exception) {
            $io->error("failed to load xlsx: " . $xlsx);
            throw  $exception;
        }

        $collection = new DomainCollection();
        $sheets = $spreadsheet->getAllSheets();
        foreach ($sheets as $sheet) {
            $domainName = $sheet->getTitle();
            $domain = $collection->getDomain($domainName);
            $locales = $this->getLocales($sheet);
            $domain->setLocales($locales);
            $row = 2;
            $keyName = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            while ($keyName !== null) {
                $key = $domain->getKey($keyName);

                $column = 1;
                foreach ($locales as $locale) {
                    $translationValue = $sheet->getCellByColumnAndRow(1 + $column, $row)->getValue();
                    $key->addValueForLocale($locale, $translationValue);
                    $column++;
                }
                $row++;
                $keyName = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            };

        }

        if (!file_exists($outputPath)) {
            mkdir($outputPath, 0777, true);
        }

        CollectionToYamlFileDumper::collectionToFile($collection, $outputPath);


        $io->success('yaml files generated.');
    }

    protected function getLocales(Worksheet $sheet)
    {
        $locales = [];
        $i = 2;
        do {
            $value = $sheet->getCellByColumnAndRow($i, 1)->getValue();
            if (empty($value)) return $locales;
            $locales[] = $value;
            $i++;

        } while (true);

    }
}
