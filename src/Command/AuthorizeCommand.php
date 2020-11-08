<?php

namespace aivus\XML2Spreadsheet\Command;

use aivus\XML2Spreadsheet\Google\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AuthorizeCommand extends Command
{
    protected static $defaultName = 'app:authorize';

    private Client $googleClient;

    public function __construct(Client $googleClient)
    {
        parent::__construct();
        $this->googleClient = $googleClient;
    }

    protected function configure()
    {
        $this
            ->setDescription('Authorize an application to access user\'s Google Spreadsheets documents');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $authUrl = $this->googleClient->getAuthUrl();

        $io = new SymfonyStyle($input, $output);
        $io->title('Authorizing an application');
        $io->text('To get an access to your Google Spreadsheets authorization of the app is required.');
        $io->text('Open the following link:');
        $io->text($authUrl);

        $authCode = $io->ask('Enter verification code');
        try {
            $this->googleClient->setAccessTokenByAuthCode($authCode);
        } catch (\Exception $e) {
            // TODO: Add logging
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

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
