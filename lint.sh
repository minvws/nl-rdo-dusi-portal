#!/bin/bash


npm run build
npm run lint
npm run check-browser-compat
npm run audit

vendor/bin/sail artisan ide-helper:generate
vendor/bin/psalm
vendor/bin/phpcs
vendor/bin/phpstan analyse
vendor/bin/phpmd app/ text ruleset.phpmd.xml
php artisan security-check:now
