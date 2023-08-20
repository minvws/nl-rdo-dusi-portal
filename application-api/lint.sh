#!/bin/bash

set -xe

vendor/bin/sail artisan ide-helper:generate
vendor/bin/psalm
vendor/bin/phpcs
vendor/bin/phpmd app/ text ruleset.phpmd.xml
vendor/bin/phpstan analyse --memory-limit=256M
php artisan security-check:now
vendor/bin/sail test
