#!/bin/bash

set -e
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

composer install

vendor/bin/sail up -d --remove-orphans
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
