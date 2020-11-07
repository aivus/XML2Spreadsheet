<?php

namespace aivus\XML2Spreadsheet\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertXMLToSpreadsheetCommand extends Command
{
    protected static $defaultName = 'app:convert-xml-to-spreadsheet';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this
            ->setDescription('Convert XML file to ф Google Spreadsheet document')
            ->addArgument('');
    }

}
