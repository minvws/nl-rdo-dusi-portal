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
npm install
npm run build
bash -c "cd $USER_APP_PATH && composer install"

vendor/bin/sail up -d --remove-orphans
# vendor/bin/sail artisan key:generate

# docker-compose exec user-admin-web php artisan key:generate
docker-compose exec user-admin-web php artisan migrate

docker-compose exec user-admin-web php artisan migrate:fresh
docker-compose exec user-admin-web php artisan user:create mail@example.com user password
echo "Log in with: mail@example.com password"