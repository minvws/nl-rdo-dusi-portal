#!/bin/bash

set -xe

npm run build
npm run lint
npm run check-browser-compat
npm run audit

vendor/bin/sail artisan ide-helper:generate
vendor/bin/psalm
vendor/bin/phpcs
vendor/bin/phpstan analyse --memory-limit=256M
vendor/bin/phpmd app/ text ruleset.phpmd.xml
vendor/bin/sail test
php artisan security-check:now
