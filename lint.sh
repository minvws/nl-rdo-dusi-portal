#!/bin/bash

set -e

npm run build
npm run lint
npm run check-browser-compat
npm run audit

vendor/bin/sail artisan ide-helper:generate
vendor/bin/phpcs
vendor/bin/phpmd app/ text ruleset.phpmd.xml
vendor/bin/psalm
vendor/bin/phpstan analyse --memory-limit=-1
php artisan security-check:now
