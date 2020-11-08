XML2Spreadsheet
===============

This application provides a way to convert an XML file to Google Spreadsheet document

## Configuration

[Follow the instructions to get "Client ID" and "Client Secret"](docs/enable-google-api.md)

_Google OAuth credentials file should be stored to `config/credentials.json`_ 

## Features
### Extendability of downloaders
It's possible to extend a set of supported downloaders and add support for e.g. ssh/sftp

To do this make your own implementation of [DownloaderInterface](src/Downloader/DownloaderInterface.php) 
and add it to [DownloaderRegistry](src/Downloader/DownloaderRegistry.php) using [DI configuration](config/di.php)

### Extendability of source file parsers
It's possible to implement your own source file parser and use e.g. JSON file as source.

To do this make your own implementation of [ParserInterface](src/Parser/ParserInterface.php)
and register it using [DI configuration](config/di.php) with prefix `parser.PARSER_NAME` 
where `PARSER_NAME` is the value returned by `ParserInterface::getName` method.

After this parser can be used like:

`bin/console app:convert-to-spreadsheet -p PARSER_NAME tests/fixtures/coffee_feed.json`

### Flexible downloaders configuration
It's possible to flexible configure downloaders by using context option from the CLI:

`bin/console app:convert-to-spreadsheet -c httpMethod=POST https://example.com`

Any additional headers (e.g. cookies) could be added using context (**not implemented yet**):

`bin/console app:convert-to-spreadsheet -c httpHeader="Authorization: Bearer" https://example.com`

`bin/console app:convert-to-spreadsheet -c httpHeader="Cookies: key1=value1; key2=value2;" https://example.com`

### Receiving Google access token
Google access token could be received by using command `bin/console app:authorize`.
At the end of the process this command prints a token which can be used
in `app:convert-to-spreadsheet` command by specifying `--access-token` option.

Example:

`bin/console app:convert-to-spreadsheet --access-token=ya29.A0AfH6-some-value-printed-by-command-mShavLt2oWhve tests/fixtures/coffee_feed.xml`

_Access token automatically will be requested by `app:convert-to-spreadsheet` command if you have not set `--access-token` option._

## Supported protocols
Currently, next protocols are supported to retrieve files:

### http / https
* To download a file using GET method (default): 
`bin/console app:convert-to-spreadsheet https://raw.githubusercontent.com/aivus/XML2Spreadsheet/master/tests/fixtures/coffee_feed.xml`

* To download a file using any other http method (e.g. POST) pass a `context` option as a key-value `httpMethod=POST`
`bin/console app:convert-to-spreadsheet -c httpMethod=POST https://raw.githubusercontent.com/aivus/XML2Spreadsheet/master/tests/fixtures/coffee_feed.xml`

### ftp
* To download a file using ftp protocol (anonymously):
`bin/console app:convert-to-spreadsheet ftp://example.com/coffee_feed.xml`

* To download a file using ftp protocol (with authentication):
`bin/console app:convert-to-spreadsheet ftp://username:password@example.com/coffee_feed.xml`

### file / local path
* To use a file from local filesystem:
`bin/console app:convert-to-spreadsheet tests/fixtures/coffee_feed.xml`

* Or using `file://` protocol
`bin/console app:convert-to-spreadsheet file:///home/user/app/tests/fixtures/coffee_feed.xml`

## Limitations
* By default, each Google access token is expiring in 3600 seconds (1 hour). 
Current implementation doesn't have autorefreshing of this token. It means it's required to repeat authorization flow
after access token expired. 
* The app doesn't handle the case when source items can have different structure (optional nodes) 
* Not optimal memory usage during work with XML/streams.
