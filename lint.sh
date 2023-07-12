#!/bin/bash

set -e

npm run build
npm run lint
npm run check-browser-compat
npm run audit

vendor/bin/sail artisan ide-helper:generate
vendor/bin/psalm
vendor/bin/phpcs
vendor/bin/phpstan analyse
php artisan security-check:now
