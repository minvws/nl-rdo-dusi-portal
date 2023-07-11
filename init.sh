#!/bin/bash

set -e
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
source .env

composer install

vendor/bin/sail up -d --remove-orphans
vendor/bin/sail artisan key:generate

# Install gmp to accellerate encryption/decryption
docker-compose exec -it nl-rdo-dusi-portal-web-laravel.test-1 /bin/bash
vendor/bin/sail root-shell -c 'apt-get update; apt-get install php8.2-gmp'
