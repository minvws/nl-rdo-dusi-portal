#!/bin/bash

set -e
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
source .env

composer install
npm install
npm run build

vendor/bin/sail up -d --remove-orphans
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
