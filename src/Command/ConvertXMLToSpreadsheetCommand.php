<?php

namespace aivus\XML2Spreadsheet\Command;

use aivus\XML2Spreadsheet\Handler\ConvertHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertXMLToSpreadsheetCommand extends Command
{
    private const ARGUMENT_URI = 'uri';

    protected static $defaultName = 'app:convert-xml-to-spreadsheet';

    /**
     * @var ConvertHandler
     */
    private ConvertHandler $convertHandler;

    public function __construct(ConvertHandler $convertHandler)
    {
        parent::__construct();
        $this->convertHandler = $convertHandler;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uri = $input->getArgument(self::ARGUMENT_URI);
        $this->convertHandler->convert($uri);
        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this
            ->setDescription('Convert XML file to a Google Spreadsheet document')
            ->addArgument(self::ARGUMENT_URI, InputArgument::REQUIRED, 'Any URI path to the source file (e.g. http://domain.com/file.xml for remote usage or file.xml for local file)');

    }
}
