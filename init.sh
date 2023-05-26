#!/bin/bash

set -e
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
source .env

composer install

vendor/bin/sail up -d --remove-orphans
vendor/bin/sail artisan key:generate
