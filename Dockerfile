FROM php:7.4-cli-alpine

COPY . /xml2spreadsheet
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

WORKDIR /xml2spreadsheet

RUN /usr/bin/composer install --no-dev -o

ENTRYPOINT [ "php", "./bin/console" ]
