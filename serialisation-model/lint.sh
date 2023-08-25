#!/bin/bash

set -xe
vendor/bin/phpcs
vendor/bin/phpmd src/ text ruleset.phpmd.xml
vendor/bin/psalm --no-cache
vendor/bin/phpstan analyse --memory-limit=-1
vendor/bin/phpunit