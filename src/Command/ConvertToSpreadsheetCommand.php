<?php

namespace aivus\XML2Spreadsheet\Command;

use aivus\XML2Spreadsheet\Context;
use aivus\XML2Spreadsheet\Exception\InvalidAccessTokenException;
use aivus\XML2Spreadsheet\Google\DTO\AccessTokenHolder;
use aivus\XML2Spreadsheet\Handler\ConvertHandler;
use aivus\XML2Spreadsheet\Parser\ProductsupXMLParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConvertToSpreadsheetCommand extends Command
{
    public const ARGUMENT_URI = 'uri';
    public const OPTION_CONTEXT = 'context';
    public const OPTION_ACCESS_TOKEN = 'access-token';
    public const OPTION_PARSER_NAME = 'parser';
    public const DEFAULT_PARSER_NAME = ProductsupXMLParser::NAME;

    protected static $defaultName = 'app:convert-to-spreadsheet';

    private ConvertHandler $convertHandler;

    public function __construct(ConvertHandler $convertHandler)
    {
        parent::__construct();
        $this->convertHandler = $convertHandler;
    }

    protected function configure()
    {
        $this
            ->setDescription('Convert XML file to a Google Spreadsheet document')
            ->addArgument(self::ARGUMENT_URI, InputArgument::REQUIRED,
                'Any URI path to the source file (e.g. http://domain.com/file.xml 
                for remote usage or file.xml for local file)')
            ->addOption(self::OPTION_CONTEXT, 'c', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Additional data for the downloader')
            ->addOption(self::OPTION_ACCESS_TOKEN, 't', InputOption::VALUE_OPTIONAL,
                'Google access token')
            ->addOption(self::OPTION_PARSER_NAME, 'p', InputOption::VALUE_OPTIONAL,
                'Set custom parser for the source file', self::DEFAULT_PARSER_NAME);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $uri = $input->getArgument(self::ARGUMENT_URI);
        $accessToken = $input->getOption(self::OPTION_ACCESS_TOKEN);

        $context = $this->getContext($input);
        $context->setParserName($input->getOption(self::OPTION_PARSER_NAME));

        if ($accessToken) {
            $accessTokenHolder = AccessTokenHolder::create([]);
            $accessTokenHolder->setAccessToken($accessToken);
            $context->setAccessTokenHolder($accessTokenHolder);
        } else {
            $authorizeCommand = $this->getApplication()->find(AuthorizeCommand::getDefaultName());
            $authorizeCommand->run(new ArrayInput([]), $output);
        }

        try {
            $spreadsheetInfo = $this->convertHandler->convert($uri, $context);
        } catch (InvalidAccessTokenException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success([
            'Google Spreadsheet document successfully created and populated with provided data.',
            sprintf('Follow the link to open the doc: %s', $spreadsheetInfo->getSpreadsheetUrl()),
        ]);

        return Command::SUCCESS;
    }

    private function getContext(InputInterface $input): Context
    {
        $context = new Context();

        $cliContextOptions = $input->getOption(self::OPTION_CONTEXT);
        foreach ($cliContextOptions as $options) {
            [$key, $value] = explode('=', $options, 2);
            $context->setOption($key, $value);
        }

        return $context;
    }
}
