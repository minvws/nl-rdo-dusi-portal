#!/bin/bash

set -e
vendor/bin/sail artisan ide-helper:generate
vendor/bin/phpcs
vendor/bin/phpmd app/ text ruleset.phpmd.xml
vendor/bin/psalm --no-cache
vendor/bin/phpstan analyse --memory-limit=-1
php artisan security-check:now
