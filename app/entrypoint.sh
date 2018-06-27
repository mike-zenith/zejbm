#!/bin/sh

docker-php-ext-install bcmath
docker-php-ext-install mbstring

./vendor/bin/phpunit
./vendor/bin/behat
