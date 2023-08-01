#!/bin/bash

set -xe

vendor/bin/phpcs app routes
vendor/bin/phpmd app/ text ruleset.phpmd.xml
vendor/bin/psalm --no-cache
vendor/bin/phpstan analyse --memory-limit=-1
php artisan security-check:now
