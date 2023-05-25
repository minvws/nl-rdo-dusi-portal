#!/bin/bash

set -e
if [ ! -f ".env" ]; then
    cp .env.example .env
fi
if [ ! -f ".env.user" ]; then
    cp .env.user.example .env.user
fi
source .env

composer install

vendor/bin/sail up -d --remove-orphans
vendor/bin/sail artisan key:generate
