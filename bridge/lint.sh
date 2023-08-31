#!/bin/bash

set -xe
vendor/bin/phpcs
vendor/bin/phpmd src/ text ruleset.phpmd.xml
vendor/bin/phpstan analyse --memory-limit=-1
docker compose up -d
sleep 5
docker compose run --rm php vendor/bin/phpunit