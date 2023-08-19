#!/bin/bash

set -e
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

composer install
npm install
npm run build

vendor/bin/sail up -d --remove-orphans
vendor/bin/sail artisan key:generate

# TODO: Run migration to default database
#docker-compose exec user-admin-web php artisan migrate
#
#docker-compose exec user-admin-web php artisan migrate:fresh
#docker-compose exec user-admin-web php artisan user:create mail@example.com user password
#echo "Log in with: mail@example.com password"
