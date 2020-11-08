<?php

namespace aivus\XML2Spreadsheet\Command;

use aivus\XML2Spreadsheet\Handler\ConvertHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertXMLToSpreadsheetCommand extends Command
{
    private const ARGUMENT_URI = 'uri';
    private const OPTION_CONTEXT = 'context';

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
        $context = $this->getContext($input);
        $this->convertHandler->convert($uri, $context);

        return Command::SUCCESS;
    }

    protected function configure()
    {
        $this
            ->setDescription('Convert XML file to a Google Spreadsheet document')
            ->addArgument(self::ARGUMENT_URI, InputArgument::REQUIRED,
                'Any URI path to the source file (e.g. http://domain.com/file.xml 
                for remote usage or file.xml for local file)')
            ->addOption(self::OPTION_CONTEXT, 'c', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Additional data for the downloader');

    }

    private function getContext(InputInterface $input)
    {
        $context = [];

        $cliContextOptions = $input->getOption(self::OPTION_CONTEXT);
        foreach ($cliContextOptions as $options) {
            [$key, $value] = explode('=', $options, 2);
            $context[$key] = $value;
        }

        return $context;
    }
}
