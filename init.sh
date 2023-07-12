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
vendor/bin/sail root-shell -c 'apt-get update; apt-get install php8.2-gmp'
vendor/bin/sail restart
