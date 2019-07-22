<?php

namespace App\Command;

use App\Translation\CollectionFactory;
use App\Util\CollectionUtil;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class YamlXlsxCommand extends Command
{
    protected static $defaultName = 'yaml:xlsx';

    protected function configure()
    {
        $this
            ->setDescription("convert yaml translation files to xlsx.")
            ->addArgument('path', InputArgument::REQUIRED, 'Where to search')
            ->addArgument('name', InputArgument::REQUIRED, 'The name to match (please read https://symfony.com/doc/current/components/finder.html)')
            ->addArgument('output', InputArgument::REQUIRED, 'Where to store new generated files')
            ->addArgument('locales', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Specify required languages, so it will create file even if the domain is empty for that language.');;;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $path = $input->getArgument('path');
        $name = $input->getArgument('name');
        $outputPath = $input->getArgument('output');
        $requiredLanguages = $input->getArgument('locales');

        $finder = new Finder();
        $finder->files()->in($path)->name($name);
        $collection = CollectionFactory::createFromFinder($finder);
        $locales = CollectionUtil::getLocales($collection, $requiredLanguages);
        $associativeArray = CollectionUtil::toArrayKeyFirst($collection, $locales);

        if (file_exists($outputPath)) {
            unlink($outputPath);
        }

        $headers = array_merge(['KEY'], $locales);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);


        foreach ($collection->getDomains() as $domain) {
            $worksheet = $spreadsheet->createSheet();
            $worksheet->setTitle($domain->getName());
            $rows = array_merge([$headers], $this->convertAssocToRow($associativeArray[$domain->getName()], $locales));
            $worksheet->fromArray($rows, null, 'A1');


            $worksheet->getColumnDimension('A')->setAutoSize(true);
            foreach (range('B', 'Z') as $letter) {
                $worksheet->getColumnDimension($letter)->setWidth(50);
            }

            $max = 1048576;
            foreach (range('B', 'Z') as $letter) {
                $i = 2;
                do {
                    $cellName = sprintf('%s%s', $letter, $i);
                    $cellValue = $worksheet->getCell($cellName)->getValue();
                    $worksheet->getStyle($cellName)->getAlignment()->setWrapText(true);
                    $i++;

                } while (!empty($cellValue) AND $i < $max);
            }
        }


        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);
        $io->success('Xlsx file generated.');
    }

    private function convertAssocToRow($assoc, $locales)
    {
        $rows = [];
        foreach ($assoc as $key => $translations) {
            $row = [$key];
            foreach ($locales as $locale) {
                $row[] = $assoc[$key][$locale];
            }
            $rows[] = $row;
        }
        return $rows;
    }
}
