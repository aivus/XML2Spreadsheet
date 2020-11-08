<?php

namespace aivus\XML2Spreadsheet\Command;

use aivus\XML2Spreadsheet\Google\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AuthorizeCommand extends Command
{
    protected static $defaultName = 'app:authorize';

    private Client $googleClient;
    private LoggerInterface $logger;

    public function __construct(Client $googleClient, LoggerInterface $logger)
    {
        parent::__construct();
        $this->googleClient = $googleClient;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setDescription('Authorize an application to access user\'s Google Spreadsheets documents');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->info('Requesting auth URL from the Google');
        $authUrl = $this->googleClient->getAuthUrl();

        $io = new SymfonyStyle($input, $output);
        $io->title('Authorizing an application');
        $io->text('To get an access to your Google Spreadsheets authorization of the app is required.');
        $io->text('Open the following link:');
        $io->text($authUrl);

        $authCode = $io->ask('Enter verification code');
        $this->logger->debug('User entered verification code. Going to exchange it on access token');
        try {
            $this->googleClient->setAccessTokenByAuthCode($authCode);
        } catch (\Exception $e) {
            $this->logger->error('Can not set access token by auth code', ['exception' => $e]);
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $this->logger->debug('Auth code successfully exchanged for access token');
        $accessTokenHolder = $this->googleClient->getAccessTokenHolder();

        $io->title('We\'ve successfully received access token from Google.');

        $io->text(sprintf(
            'You can use it in "<fg=green>%s</>" command by option "<fg=green>--%s=%s</>".',
            ConvertToSpreadsheetCommand::getDefaultName(),
            ConvertToSpreadsheetCommand::OPTION_ACCESS_TOKEN,
            $accessTokenHolder->getAccessToken(),
        ));

        $io->note(sprintf(
            'This token will expire in %d seconds.' . PHP_EOL .
            'After that time authorization flow should be repeated to get new access token',
            $accessTokenHolder->getExpiresIn()
        ));

        return Command::SUCCESS;

    }
}
