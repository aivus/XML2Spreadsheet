<?php

namespace aivus\SpreadsheetConverter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertCommand extends Command
{
    protected static $defaultName = 'app:convert';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this
            ->setDescription('Convert a file to Google Spreadsheet document')
            ->addArgument('');
    }
}
