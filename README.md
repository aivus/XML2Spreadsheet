XML2Spreadsheet
===============

This application provides a way to convert an XML file to Google Spreadsheet document

## Configuration

[Follow the instructions to get "Client ID" and "Client Secret"](docs/enable-google-api.md)

## Supported protocols

The app supports next protocols to retrieve files:
### http / https

Examples:
* To download a file using GET method (default) 

`bin/console app:convert-xml-to-spreadsheet https://raw.githubusercontent.com/aivus/XML2Spreadsheet/master/tests/fixtures/coffee_feed.xml`

* To download a file using any other http method (e.g. POST) pass a `context` option as a key-value `httpMethod=POST`

`bin/console app:convert-xml-to-spreadsheet -c httpMethod=POST https://raw.githubusercontent.com/aivus/XML2Spreadsheet/master/tests/fixtures/coffee_feed.xml`

### ftp
_TBD_

### file / _local path_
_TBD_
