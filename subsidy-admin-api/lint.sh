#!/bin/bash

set -xe

vendor/bin/sail artisan ide-helper:generate
vendor/bin/psalm
vendor/bin/phpcs
vendor/bin/phpmd app/ text ruleset.phpmd.xml
vendor/bin/phpstan analyse app routes
php artisan security-check:now
vendor/bin/sail test
