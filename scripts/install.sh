#!/usr/bin/env bash

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs

cp .env.sail.example .env

./vendor/bin/sail up -d

./vendor/bin/sail artisan key:generate
